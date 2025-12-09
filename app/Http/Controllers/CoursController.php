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
    public function modify($id)
    {
        $cours = Cours::with(['classe.niveau', 'professeur'])
            ->findOrFail($id);

        return view('pages.modifier_cours', compact('cours'));
    }

    /**
     * Supprime un cours
     */
    public function delete($id)
    {
        $cours = Cours::findOrFail($id);

        // Vérifier s'il y a des évaluations liées
        if ($cours->evaluations()->exists()) {
            return back()->withStatus('Impossible de supprimer ce cours, des évaluations y sont liées !');
        }

        $cours->delete();

        return back()->withStatus('Cours supprimé avec succès');
    }

    /**
     * Crée un nouveau cours
     */
    public function store(Request $request)
    {
        $request->validate([
            'cours' => 'required|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'semestre' => 'required|in:1,2',
            'annee_id' => 'required|exists:annee_academique,id',
        ]);

        $campusId = Auth::user()->campus_id;

        // Vérifier si le cours existe déjà
        $exists = Cours::where('libelle_cours', $request->cours)
            ->where('id_classe', $request->classe_id)
            ->where('id_professeur', $request->professeur_id)
            ->where('semestre', $request->semestre)
            ->where('campus_id', $campusId)
            ->where('annee_id', $request->annee_id)
            ->exists();

        if ($exists) {
            return back()->withStatus('Ce cours existe déjà');
        }

        Cours::create([
            'libelle_cours' => $request->cours,
            'id_classe' => $request->classe_id,
            'id_professeur' => $request->professeur_id,
            'semestre' => $request->semestre,
            'campus_id' => $campusId,
            'annee_id' => $request->annee_id,
            'etat' => Cours::ETAT_INACTIF,
        ]);

        return back()->withStatus('Cours ajouté avec succès');
    }

    /**
     * Met à jour un cours
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_cours' => 'required|exists:cours,id_cours',
            'libelle_cours' => 'required|string|max:255',
            'id_classe' => 'required|exists:classes,id',
            'id_professeur' => 'required|exists:professeurs,id',
        ]);

        Cours::where('id_cours', $request->id_cours)->update([
            'libelle_cours' => $request->libelle_cours,
            'id_classe' => $request->id_classe,
            'id_professeur' => $request->id_professeur,
        ]);

        return back()->withStatus('Cours modifié avec succès');
    }

    /**
     * Page d'activation des évaluations
     */
    public function activationCours()
    {
        $an = AnneeAcademique::orderByDesc('annee1')->get();
        return view('pages.activation', compact('an'));
    }

    /**
     * Change l'évaluation active (année/semestre)
     */
    public function changeEvaluationActive(Request $request)
    {
        $request->validate([
            'annee' => 'required|exists:annee_academique,id',
            'semestre' => 'required|in:1,2',
        ]);

        $campusId = Auth::user()->campus_id;

        // Désactiver tous les cours du campus
        Cours::where('campus_id', $campusId)
            ->update(['etat' => Cours::ETAT_INACTIF]);

        // Réinitialiser le statut des étudiants du campus
        Etudiant::where('campus_id', $campusId)
            ->update(['statut' => Etudiant::STATUT_INACTIF]);

        // Activer les cours de l'année/semestre sélectionnés
        Cours::where('campus_id', $campusId)
            ->where('semestre', $request->semestre)
            ->where('annee_id', $request->annee)
            ->update(['etat' => Cours::ETAT_ACTIF]);

        return back()->withStatus('Évaluation en cours changée avec succès');
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
            return 'Pas de cours actif';
        }

        return $info->anneeAcademique->annee1 . '-' . $info->anneeAcademique->annee2 
            . ' / Semestre ' . $info->semestre;
    }

    /**
     * Récupère les infos de l'évaluation active pour les rapports
     */
    public static function getEvaluationForRapportProf()
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
