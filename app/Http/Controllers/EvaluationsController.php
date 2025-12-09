<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Niveau;
use App\Models\Professeur;
use App\Models\Question;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationsController extends Controller
{
    /**
     * Page d'accueil - Saisie du matricule
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('pages.evaluation');
    }

    /**
     * Vérifier le matricule et afficher le formulaire d'évaluation
     */
    public function checkMatricule(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'matricule' => 'required|string|max:20'
        ]);

        // Nettoyer le matricule (enlever tirets et espaces)
        $matricule = $this->cleanMatricule($request->matricule);

        // Chercher l'étudiant
        $etudiant = Etudiant::where('matricule', $matricule)->first();

        if (!$etudiant) {
            return back()->withErrors(['matricule' => 'Matricule non trouvé dans le système.'])
                         ->withInput();
        }

        // Vérifier si l'étudiant a déjà évalué
        if ($etudiant->aDejaEvalue()) {
            return back()->withErrors(['matricule' => 'Vous avez déjà effectué votre évaluation pour ce semestre.'])
                         ->withInput();
        }

        // Récupérer les cours actifs de la classe de l'étudiant
        $cours = Cours::with('professeur')
            ->where('id_classe', $etudiant->id_classe)
            ->where('etat', Cours::ETAT_ACTIF)
            ->get();

        if ($cours->isEmpty()) {
            return back()->withErrors(['matricule' => 'Aucun cours actif trouvé pour votre classe.'])
                         ->withInput();
        }

        // Récupérer toutes les questions
        $questions = Question::orderBy('idQ')->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['matricule' => 'Aucune question d\'évaluation configurée.'])
                         ->withInput();
        }

        // Récupérer la classe
        $classe = Classes::find($etudiant->id_classe);

        // Formater le matricule pour l'affichage (ex: 202400101 -> 202-40-0101)
        $matricule_formate = $this->formatMatricule($matricule);

        return view('pages.evaluation-page', compact(
            'etudiant',
            'cours',
            'questions',
            'classe',
            'matricule',
            'matricule_formate'
        ));
    }

    /**
     * Enregistrer les évaluations
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string',
            'etudiant_id' => 'required|integer|exists:etudiants,id',
            'evaluations' => 'required|array',
            'commentaires' => 'nullable|array'
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);

        // Vérifier à nouveau si l'étudiant n'a pas déjà évalué
        if ($etudiant->aDejaEvalue()) {
            return redirect()->route('evaluation')
                ->withErrors(['matricule' => 'Vous avez déjà effectué votre évaluation.']);
        }

        DB::beginTransaction();

        try {
            // Enregistrer les évaluations
            foreach ($request->evaluations as $coursId => $profData) {
                foreach ($profData as $profId => $questions) {
                    foreach ($questions as $questionId => $note) {
                        if (!empty($note)) {
                            Evaluation::create([
                                'id_professeur' => $profId,
                                'id_cours' => $coursId,
                                'idQ' => $questionId,
                                'note' => (int) $note
                            ]);
                        }
                    }
                }
            }

            // Enregistrer les commentaires
            if ($request->has('commentaires')) {
                foreach ($request->commentaires as $coursId => $profData) {
                    foreach ($profData as $profId => $commentaire) {
                        if (!empty(trim($commentaire))) {
                            Commentaire::create([
                                'commentaire' => trim($commentaire),
                                'id_etudiant' => $etudiant->id,
                                'id_professeur' => $profId,
                                'id_cours' => $coursId
                            ]);
                        }
                    }
                }
            }

            // Marquer l'étudiant comme ayant évalué
            $etudiant->marquerCommeEvalue();

            DB::commit();

            return redirect()->route('evaluation.success');

        } catch (\Exception $e) {
            DB::rollBack();

            // Changer back() par redirect()->route('evaluation')
            return redirect()->route('evaluation')
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()]);
        }
    }

    /**
     * Page de succès après évaluation
     */
    public function success(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('pages.success');
    }

    /**
     * API: Résultats par classe (AJAX pour résultat général)
     */
    public function getResultatClasse($classeId): \Illuminate\Http\JsonResponse
    {
        $classe = Classes::with('niveau')->findOrFail($classeId);
        $campusId = Auth::user()->campus_id;

        // Cours actifs de cette classe
        $coursQuery = Cours::where('id_classe', $classeId)
            ->where('campus_id', $campusId)
            ->where('etat', 1)
            ->with('professeur')
            ->get();

        // Questions
        $questions = Question::orderBy('idQ')->get();

        // Construire les données
        $coursData = [];
        foreach ($coursQuery as $c) {
            $notes = [];
            foreach ($questions as $q) {
                $note = Evaluation::where('id_cours', $c->id_cours)
                    ->where('id_professeur', $c->id_professeur)
                    ->where('idQ', $q->idQ)
                    ->avg('note');
                $notes[] = $note ? round($note) : 0;
            }

            $coursData[] = [
                'professeur' => $c->professeur->full_name ?? 'N/A',
                'libelle' => $c->libelle_cours,
                'notes' => $notes
            ];
        }
        // Stats classe
        $nbEtudiants = Etudiant::where('id_classe', $classeId)->count();
        $tauxParticipation = self::getTauxDeParticipation($classeId);

        return response()->json([
            'classe' => [
                'id' => $classe->id,
                'libelle' => $classe->libelle,
                'niveau' => $classe->niveau->libelle_niveau ?? '',
                'nbEtudiants' => $nbEtudiants,
                'tauxParticipation' => $tauxParticipation
            ],
            'cours' => $coursData
        ]);
    }

    /**
     * Liste des niveaux
     */
    public function getListeNiveau(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campusId = Auth::user()->campus_id;

        $niveaux = Niveau::whereHas('classes', function($q) use ($campusId) {
            $q->where('campus_id', $campusId);
        })->orderBy('libelle_niveau')->get();

        return view('pages.lists.liste-niveau', compact('niveaux'));
    }


    /**
     * Résultat par niveau
     */
    public function getResultatNiveau($id_niveau): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campusId = Auth::user()->campus_id;

        $niveau = Niveau::findOrFail($id_niveau);

        // Classes de ce niveau
        $classes = Classes::where('id_niveau', $id_niveau)
            ->where('campus_id', $campusId)
            ->get();

        // Cours de ces classes
        $cours = Cours::whereIn('id_classe', $classes->pluck('id'))
            ->where('campus_id', $campusId)
            ->where('etat', 1)
            ->with(['professeur', 'classe'])
            ->get();

        // Questions
        $questions = Question::orderBy('idQ')->get();

        // Taux de participation du niveau
        $tauxParticipation = self::getTauxDeParticipationNiveau($id_niveau);

        return view('pages.resultat_niveau', compact('niveau', 'classes', 'cours', 'questions', 'tauxParticipation'));
    }

    /**
     * Résultat détaillé d'un professeur
     */
    public function getResultatProf($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campusId = Auth::user()->campus_id;

        $prof = Professeur::findOrFail($id);

        // Cours du professeur
        $cours = Cours::where('id_professeur', $id)
            ->where('campus_id', $campusId)
            ->where('etat', 1)
            ->with(['classe.niveau'])
            ->get();

        // Questions
        $questions = Question::orderBy('idQ')->get();

        // Note finale
        $noteFinale = self::getNoteFinale($id);

        return view('pages.resultat_prof', compact('prof', 'cours', 'questions', 'noteFinale'));
    }

    /**
     * Liste des enseignants avec notes
     */
    public function getEnseignant(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campus_id = Auth::user()->campus_id;

        $professeurs = Professeur::whereHas('cours', function($q) use ($campus_id) {
            $q->where('campus_id', $campus_id)
                ->where('etat', 1);
        })
            ->orderBy('full_name')
            ->get();

        // Calculer la moyenne pour chaque professeur
        foreach ($professeurs as $prof) {
            $coursIds = Cours::where('id_professeur', $prof->id)
                ->where('campus_id', $campus_id)
                ->where('etat', 1)
                ->pluck('id_cours')
                ->toArray();

            if (empty($coursIds)) {
                $prof->moyenne = 0;
                continue;
            }

            // Moyenne des notes (note est déjà un entier)
            $moyenne = Evaluation::whereIn('id_cours', $coursIds)
                ->avg('note');

            $prof->moyenne = $moyenne ? round($moyenne, 1) : 0;
        }

        return view('pages.lists.liste_prof', compact('professeurs'));
    }

    /**
     * Nettoyer le matricule (enlever tirets, espaces, etc.)
     */
    private function cleanMatricule(string $matricule): string
    {
        // Enlever seulement les tirets et espaces, garder les lettres et chiffres
        return preg_replace('/[\s\-]/', '', $matricule);
    }

    /**
     * Résultat général (par classe avec AJAX)
     */
    public function getResultatGeneral(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campusId = Auth::user()->campus_id;

        $classes = Classes::where('campus_id', $campusId)
            ->with('niveau')
            ->orderBy('libelle')
            ->get();

        $profs = Professeur::whereHas('cours', function($q) use ($campusId) {
            $q->where('campus_id', $campusId)->where('etat', 1);
        })->count();

        $questions = Question::orderBy('idQ')->get();

        return view('pages.resultat_general', compact('classes', 'profs', 'questions'));
    }

    /**
     * Formater le matricule pour l'affichage
     */
    private function formatMatricule(string $matricule): string
    {
        // Format: XXX-XX-XXXX
        if (strlen($matricule) >= 9) {
            return substr($matricule, 0, 3) . '-' . substr($matricule, 3, 2) . '-' . substr($matricule, 5);
        }
        return $matricule;
    }

    // ==========================================
    // MÉTHODES STATIQUES POUR LES VUES/RAPPORTS
    // ==========================================

    /**
     * Calculer le pourcentage pour une question/prof/cours
     */
    public static function getPourcent($id_niveau, $classe, $idQ, $idProf, $id_cours): int
    {
        $result = DB::table('evaluations')
            ->where('idQ', $idQ)
            ->where('id_professeur', $idProf)
            ->where('id_cours', $id_cours)
            ->avg('note');

        return $result ? (int) round($result) : 0;
    }

    /**
     * Pourcentage par niveau
     */
    public static function getPourcentByNiveau($niveau, $idQ, $idProf): int
    {
        $result = DB::table('evaluations as e')
            ->join('cours as c', 'e.id_cours', '=', 'c.id_cours')
            ->join('classes as cl', 'c.id_classe', '=', 'cl.id')
            ->where('cl.id_niveau', $niveau)
            ->where('e.idQ', $idQ)
            ->where('e.id_professeur', $idProf)
            ->avg('e.note');

        return $result ? (int) round($result) : 0;
    }

    /**
     * Pourcentage par cours
     */
    public static function getPourcentByCours($id_cours, $idQ, $idProf): int
    {
        $result = DB::table('evaluations')
            ->where('id_cours', $id_cours)
            ->where('idQ', $idQ)
            ->where('id_professeur', $idProf)
            ->avg('note');

        return $result ? (int) round($result) : 0;
    }

    /**
     * Note finale d'un professeur (moyenne générale)
     */
    public static function getNoteFinale($idProf): int
    {
        $result = DB::table('evaluations')
            ->where('id_professeur', $idProf)
            ->avg('note');

        return $result ? (int) round($result) : 0;
    }

    /**
     * Taux de participation d'une classe
     */
    public static function getTauxDeParticipation($classeId): int
    {
        $total = Etudiant::where('id_classe', $classeId)->count();
        if ($total === 0) return 0;

        $evalues = Etudiant::where('id_classe', $classeId)
            ->where('statut', Etudiant::STATUT_A_EVALUE)
            ->count();

        return (int) round(($evalues / $total) * 100);
    }

    /**
     * Taux de participation par niveau
     */
    public static function getTauxDeParticipationNiveau($id_niveau): float|int
    {
        $campus_id = Auth::user()->campus_id;

        // Total étudiants du niveau pour ce campus
        $totalEtudiants = DB::table('etudiants as e')
            ->join('classes as c', 'e.id_classe', '=', 'c.id')
            ->where('c.id_niveau', $id_niveau)
            ->where('e.campus_id', $campus_id)
            ->count();

        if ($totalEtudiants == 0) {
            return 0;
        }

        // Étudiants ayant évalué (statut = 2)
        $etudiantsEvalue = DB::table('etudiants as e')
            ->join('classes as c', 'e.id_classe', '=', 'c.id')
            ->where('c.id_niveau', $id_niveau)
            ->where('e.campus_id', $campus_id)
            ->where('e.statut', 2)
            ->count();

        return round(($etudiantsEvalue / $totalEtudiants) * 100);
    }
    /**
     * Récupérer les commentaires pour un prof/classe/cours
     */
    public static function getCommentaire($idProf, $classe, $id_cours)
    {
        return Commentaire::where('id_professeur', $idProf)
            ->where('id_cours', $id_cours)
            ->whereNotNull('commentaire')
            ->where('commentaire', '!=', '')
            ->get();
    }

    /**
     * Récupérer les commentaires par prof et cours
     */
    public static function getCommentaireByProf($idProf, $idCours)
    {
        return Commentaire::where('id_professeur', $idProf)
            ->where('id_cours', $idCours)
            ->whereNotNull('commentaire')
            ->where('commentaire', '!=', '')
            ->pluck('commentaire');
    }

    /**
     * Nombre total d'évaluations pour un cours
     */
    public static function getNombreEvaluations($idCours): int
    {
        return Evaluation::where('id_cours', $idCours)
            ->distinct('created_at')
            ->count();
    }
}
