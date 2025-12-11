<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Cours;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoursController extends Controller
{
    /**
     * Affiche le formulaire de modification d'un cours
     */
    public function modify($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $cours = Cours::with(['classe.niveau', 'professeur'])
            ->findOrFail($id);

        return view('pages.modifier_cours', compact('cours'));
    }

    /**
     * Supprime un cours
     */
    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        $cours = Cours::findOrFail($id);

        // Vérifier s'il y a des évaluations liées
        if ($cours->evaluations()->exists()) {
            return redirect()->route('tools')
                ->with('status', 'Impossible de supprimer ce cours car des évaluations y sont associées.')
                ->with('redirect_tab', 'cours');
        }

        $cours->delete();

        return redirect()->route('tools')
            ->with('status', 'Cours supprimé avec succès.')
            ->with('redirect_tab', 'cours');
    }

    /**
     * Crée un nouveau cours (utilise automatiquement l'année/semestre actifs)
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'libelle_cours' => 'required|string|max:255',
            'id_classe' => 'required|exists:classes,id',
            'id_professeur' => 'required|exists:professeurs,id',
            'semestre' => 'required|in:1,2',
            'annee_id' => 'required|exists:annee_academique,id',
        ], [
            'libelle_cours.required' => 'Le libellé du cours est obligatoire.',
            'libelle_cours.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'id_classe.required' => 'Veuillez sélectionner une classe.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
            'id_professeur.required' => 'Veuillez sélectionner un professeur.',
            'id_professeur.exists' => 'Le professeur sélectionné n\'existe pas.',
            'semestre.required' => 'Le semestre est obligatoire.',
            'semestre.in' => 'Le semestre doit être 1 ou 2.',
            'annee_id.required' => 'L\'année académique est obligatoire.',
            'annee_id.exists' => 'L\'année académique sélectionnée n\'existe pas.',
        ]);

        $campusId = Auth::user()->campus_id;

        // Vérifier si le cours existe déjà
        $exists = Cours::where('libelle_cours', $request->libelle_cours)
            ->where('id_classe', $request->id_classe)
            ->where('id_professeur', $request->id_professeur)
            ->where('semestre', $request->semestre)
            ->where('campus_id', $campusId)
            ->where('annee_id', $request->annee_id)
            ->exists();

        if ($exists) {
            return redirect()->route('tools')
                ->with('status', 'Erreur : Ce cours existe déjà pour cette classe, ce professeur et cette période.')
                ->with('redirect_tab', 'cours');
        }

        Cours::create([
            'libelle_cours' => $request->libelle_cours,
            'id_classe' => $request->id_classe,
            'id_professeur' => $request->id_professeur,
            'semestre' => $request->semestre,
            'campus_id' => $campusId,
            'annee_id' => $request->annee_id,
            'etat' => Cours::ETAT_INACTIF,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Cours ajouté avec succès.')
            ->with('redirect_tab', 'cours');
    }

    /**
     * Met à jour un cours
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id_cours' => 'required|exists:cours,id_cours',
            'libelle_cours' => 'required|string|max:255',
            'id_classe' => 'required|exists:classes,id',
            'id_professeur' => 'required|exists:professeurs,id',
        ], [
            'id_cours.required' => 'L\'identifiant du cours est requis.',
            'id_cours.exists' => 'Ce cours n\'existe pas.',
            'libelle_cours.required' => 'Le libellé du cours est obligatoire.',
            'id_classe.required' => 'Veuillez sélectionner une classe.',
            'id_professeur.required' => 'Veuillez sélectionner un professeur.',
        ]);

        Cours::where('id_cours', $request->id_cours)->update([
            'libelle_cours' => $request->libelle_cours,
            'id_classe' => $request->id_classe,
            'id_professeur' => $request->id_professeur,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Cours modifié avec succès.')
            ->with('redirect_tab', 'cours');
    }

    /**
     * Page d'activation des évaluations
     */
    public function activationCours(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $an = AnneeAcademique::orderByDesc('annee1')->get();
        return view('pages.activation', compact('an'));
    }

    /**
     * Change l'évaluation active (année/semestre)
     */
    public function changeEvaluationActive(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'annee' => 'required|exists:annee_academique,id',
            'semestre' => 'required|in:1,2',
        ], [
            'annee.required' => 'Veuillez sélectionner une année académique.',
            'annee.exists' => 'L\'année académique sélectionnée n\'existe pas.',
            'semestre.required' => 'Veuillez sélectionner un semestre.',
            'semestre.in' => 'Le semestre doit être 1 ou 2.',
        ]);

        $campusId = Auth::user()->campus_id;

        // Désactiver tous les cours du campus
        Cours::where('campus_id', $campusId)
            ->update(['etat' => Cours::ETAT_INACTIF]);

        // Réinitialiser le statut des étudiants du campus
        Etudiant::where('campus_id', $campusId)
            ->update(['statut' => Etudiant::STATUT_INACTIF]);

        // Activer les cours de l'année/semestre sélectionnés
        $nbCoursActives = Cours::where('campus_id', $campusId)
            ->where('semestre', $request->semestre)
            ->where('annee_id', $request->annee)
            ->update(['etat' => Cours::ETAT_ACTIF]);

        return back()->with('status', "Période d'évaluation changée avec succès. $nbCoursActives cours activé(s).");
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes statiques (utilisées dans les vues)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère l'année académique d'un cours
     */
    public static function getAnneeByIdCours($id_cours): string
    {
        $cours = Cours::with('anneeAcademique')->find($id_cours);

        if (!$cours || !$cours->anneeAcademique) {
            return 'N/A';
        }

        return $cours->anneeAcademique->annee1 . '/' . $cours->anneeAcademique->annee2;
    }

    /**
     * Récupère toutes les années académiques
     */
    public static function getAllAnneeAca()
    {
        return AnneeAcademique::orderByDesc('annee1')->get();
    }

    /**
     * Récupère l'évaluation active (année/semestre)
     */
    public static function getEvaluationActive(): string
    {
        $campusId = Auth::user()->campus_id;

        $info = Cours::with('anneeAcademique')
            ->where('campus_id', $campusId)
            ->where('etat', Cours::ETAT_ACTIF)
            ->first();

        if (!$info || !$info->anneeAcademique) {
            return 'Aucune période active';
        }

        return $info->anneeAcademique->annee1 . '-' . $info->anneeAcademique->annee2
            . ' / Semestre ' . $info->semestre;
    }

    /**
     * Récupère les infos de l'évaluation active pour les rapports
     */
    public static function getEvaluationForRapportProf(): ?object
    {
        $campusId = Auth::user()->campus_id;

        $cours = Cours::with('anneeAcademique')
            ->where('campus_id', $campusId)
            ->where('etat', Cours::ETAT_ACTIF)
            ->first();

        if (!$cours) {
            return null;
        }

        return (object) [
            'semestre' => $cours->semestre,
            'annee1' => $cours->anneeAcademique->annee1,
            'annee2' => $cours->anneeAcademique->annee2,
        ];
    }
}
