<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classes;
use App\Models\Commentaire;
use App\Models\Cours;
use App\Models\Evaluation;
use App\Models\Niveau;
use App\Models\Professeur;
use App\Models\Question;
use App\Mail\RapportProfesseurMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ProfesseursController extends Controller
{
    /**
     * Page principale avec tous les outils (profs, cours, classes, etc.)
     */
    public function getAll(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        $campus_id = $user->campus_id;

        return view('pages.diplome-tools', [
            'professeurs' => Professeur::orderBy('full_name')->get(),
            'questions' => Question::orderBy('idQ')->get(),
            'niveaux' => Niveau::all(),
            'classes' => Classes::where('campus_id', $campus_id)->get(),
            'annees' => AnneeAcademique::all(),
            'cours' => Cours::whereHas('classe', function($q) use ($campus_id) {
                $q->where('campus_id', $campus_id);
            })->get(),
        ]);
    }

    /**
     * Liste tous les professeurs
     */
    public static function getListProfs()
    {
        return Professeur::orderBy('full_name')->get();
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $professeurs = Professeur::findOrFail($id);
        return view('pages.edit-professeur', compact('professeurs'));
    }

    /**
     * Supprime un professeur
     */
    public function delete($id)
    {
        $professeur = Professeur::findOrFail($id);

        if ($professeur->cours()->exists()) {
            return back()->withStatus('Impossible, ce professeur intervient dans un cours !');
        }

        $professeur->delete();

        return back()->withStatus('Professeur supprimé avec succès');
    }

    /**
     * Modifie un professeur
     */
    public function modify(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:professeurs,id',
            'professeur' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Professeur::where('id', $request->id)->update([
            'full_name' => $request->professeur,
            'email' => $request->email,
        ]);

        return back()->withStatus('Professeur modifié avec succès');
    }

    /**
     * Crée un nouveau professeur
     */
    public function store(Request $request)
    {
        $request->validate([
            'professeur' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $exists = Professeur::where('full_name', $request->professeur)->exists();

        if ($exists) {
            return back()->withStatus('Ce professeur existe déjà');
        }

        Professeur::create([
            'full_name' => $request->professeur,
            'email' => $request->email,
        ]);

        return back()->withStatus('Professeur ajouté avec succès');
    }

    /**
     * Générer le rapport PDF d'un professeur
     */
    public function getRapport($id): \Illuminate\Http\Response
    {
        $prof = Professeur::findOrFail($id);
        $pdfData = $this->generateRapportData($prof);

        $pdf = Pdf::loadView('pdf.rapport-prof', $pdfData);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('rapport_' . Str::slug($prof->full_name) . '.pdf');
    }

    /**
     * Envoyer le rapport par email au professeur
     */
    public function sendRapportByEmail($id)
    {
        $prof = Professeur::findOrFail($id);

        // Vérifier si le professeur a un email
        if (empty($prof->email)) {
            return back()->withStatus('Erreur : Ce professeur n\'a pas d\'adresse email configurée.');
        }

        try {
            // Générer les données du rapport
            $pdfData = $this->generateRapportData($prof);

            // Générer le PDF
            $pdf = Pdf::loadView('pdf.rapport-prof', $pdfData);
            $pdf->setPaper('A4', 'portrait');

            // Sauvegarder temporairement le PDF
            $fileName = 'rapport_' . Str::slug($prof->full_name) . '_' . time() . '.pdf';
            $tempPath = storage_path('app/temp/' . $fileName);

            // Créer le dossier temp s'il n'existe pas
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Sauvegarder le PDF
            file_put_contents($tempPath, $pdf->output());

            // Envoyer l'email
            Mail::to($prof->email)->send(new RapportProfesseurMail(
                $prof,
                $pdfData['noteFinale'],
                $pdfData['appreciation'],
                $pdfData['evalInfo']->semestre ?? '1',
                $tempPath
            ));

            // Supprimer le fichier temporaire
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return back()->withStatus('Rapport envoyé avec succès à ' . $prof->email);

        } catch (\Exception $e) {
            return back()->withStatus('Erreur lors de l\'envoi : ' . $e->getMessage());
        }
    }

    /**
     * Envoyer les rapports à tous les professeurs ayant un email
     */
    public function sendAllRapports()
    {
        $campusId = Auth::user()->campus_id;

        // Professeurs avec email et ayant des cours actifs
        $professeurs = Professeur::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereHas('cours', function($q) use ($campusId) {
                $q->where('campus_id', $campusId)->where('etat', 1);
            })
            ->get();

        if ($professeurs->isEmpty()) {
            return back()->withStatus('Aucun professeur avec email n\'a de cours actif.');
        }

        $sent = 0;
        $errors = 0;

        foreach ($professeurs as $prof) {
            try {
                $pdfData = $this->generateRapportData($prof);

                $pdf = Pdf::loadView('pdf.rapport-prof', $pdfData);
                $pdf->setPaper('A4', 'portrait');

                $fileName = 'rapport_' . Str::slug($prof->full_name) . '_' . time() . '.pdf';
                $tempPath = storage_path('app/temp/' . $fileName);

                if (!file_exists(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }

                file_put_contents($tempPath, $pdf->output());

                Mail::to($prof->email)->send(new RapportProfesseurMail(
                    $prof,
                    $pdfData['noteFinale'],
                    $pdfData['appreciation'],
                    $pdfData['evalInfo']->semestre ?? '1',
                    $tempPath
                ));

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $sent++;
            } catch (\Exception $e) {
                $errors++;
                \Log::error('Erreur envoi rapport à ' . $prof->email . ': ' . $e->getMessage());
            }
        }

        $message = "$sent rapport(s) envoyé(s) avec succès.";
        if ($errors > 0) {
            $message .= " $errors erreur(s) d'envoi.";
        }

        return back()->withStatus($message);
    }

    /**
     * Générer les données du rapport pour un professeur
     */
    private function generateRapportData(Professeur $prof): array
    {
        $campusId = Auth::user()->campus_id;

        // Cours du professeur
        $coursQuery = Cours::where('id_professeur', $prof->id)
            ->where('campus_id', $campusId)
            ->where('etat', 1)
            ->with(['classe.niveau'])
            ->get();

        // Questions
        $questions = Question::orderBy('idQ')->get();

        // Note finale
        $noteFinale = EvaluationsController::getNoteFinale($prof->id);

        // Appréciation, objet et avertissement selon la note
        if ($noteFinale < 65) {
            $appreciation = 'peu satisfaisant';
            $objet = 'Informations';
            $avertissement = 'vous pouvez vous rapprocher des responsables de département pour plus de détails.';
        } elseif ($noteFinale <= 85) {
            $appreciation = 'satisfaisant';
            $objet = 'Remerciements et Encouragements';
            $avertissement = 'témoignant de la qualité de vos prestations.';
        } else {
            $appreciation = 'très satisfaisant';
            $objet = 'Remerciements et Encouragements';
            $avertissement = 'témoignant de la qualité de vos prestations.';
        }

        // Semestre
        $evalInfo = CoursController::getEvaluationForRapportProf();
        $semestre = $evalInfo->semestre ?? '1';
        $adjectifSemestre = ($semestre == '1') ? 'premier' : 'second';

        // Données par cours
        $cours = [];
        foreach ($coursQuery as $c) {
            $notes = [];
            foreach ($questions as $q) {
                $note = Evaluation::where('id_cours', $c->id_cours)
                    ->where('id_professeur', $prof->id)
                    ->where('idQ', $q->idQ)
                    ->avg('note');
                $notes[] = $note ? round($note) : 0;
            }

            $cours[] = [
                'id_cours' => $c->id_cours,
                'libelle' => $c->libelle_cours,
                'classe' => ($c->classe->niveau->libelle_niveau ?? '') . ' ' . $c->classe->libelle,
                'notes' => $notes,
                'moyenne' => count($notes) > 0 ? round(array_sum($notes) / count($notes), 1) : 0
            ];
        }

        return [
            'prof' => $prof,
            'cours' => $cours,
            'questions' => $questions,
            'noteFinale' => $noteFinale,
            'appreciation' => $appreciation,
            'objet' => $objet,
            'avertissement' => $avertissement,
            'evalInfo' => $evalInfo,
            'adjectifSemestre' => $adjectifSemestre
        ];
    }
}
