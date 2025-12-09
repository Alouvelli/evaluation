<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Cours;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassesController extends Controller
{
    /**
     * Liste les classes pour une année/semestre
     */
    public function getListeClasse($annee, $semestre)
    {
        $campusId = Auth::user()->campus_id;

        $classes = Classes::with(['niveau', 'cours.anneeAcademique'])
            ->join('cours', 'cours.id_classe', '=', 'classes.id')
            ->join('annee_academique', 'annee_academique.id', '=', 'cours.annee_id')
            ->where('cours.semestre', $semestre)
            ->where('cours.annee_id', $annee)
            ->where('classes.campus_id', $campusId)
            ->groupBy([
                'classes.id',
                'classes.libelle',
                'classes.campus_id',
                'classes.id_niveau',
                'classes.created_at',
                'classes.updated_at',
                'cours.semestre',
                'cours.annee_id',
                'annee_academique.annee1',
                'annee_academique.annee2',
            ])
            ->select([
                'classes.id',
                'classes.libelle',
                'classes.campus_id',
                'classes.id_niveau',
                'cours.semestre',
                'cours.annee_id',
                'annee_academique.annee1',
                'annee_academique.annee2',
            ])
            ->get();

        if ($classes->isEmpty()) {
            return back()->withStatus('Impossible, aucun cours trouvé !');
        }

        return view('pages.lists.liste-classe', compact('classes'));
    }

    /**
     * Formulaire d'édition d'une classe
     */
    public function edit($id)
    {
        $classes = Classes::with('niveau')->findOrFail($id);
        $niveaux = Niveau::all();

        return view('pages.edit-classe', compact('classes', 'niveaux'));
    }

    /**
     * Supprime une classe
     */
    public function delete($id)
    {
        $classe = Classes::findOrFail($id);

        // Vérifier qu'aucun cours n'est lié
        if ($classe->cours()->exists()) {
            return back()->withStatus('Impossible, cette classe a un cours !');
        }

        // Vérifier qu'aucun étudiant n'est lié
        if ($classe->etudiants()->exists()) {
            return back()->withStatus('Impossible, cette classe contient des étudiants !');
        }

        $classe->delete();

        return back()->withStatus('Classe supprimée avec succès');
    }

    /**
     * Crée une nouvelle classe
     */
    public function store(Request $request)
    {
        $request->validate([
            'classe' => 'required|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id_niveau',
        ]);

        $campusId = Auth::user()->campus_id;

        // Vérifier si la classe existe déjà
        $exists = Classes::where('libelle', $request->classe)
            ->where('campus_id', $campusId)
            ->where('id_niveau', $request->niveau_id)
            ->exists();

        if ($exists) {
            return back()->withStatus('Cette classe existe déjà !');
        }

        Classes::create([
            'libelle' => $request->classe,
            'id_niveau' => $request->niveau_id,
            'campus_id' => $campusId,
        ]);

        return back()->withStatus('Classe ajoutée avec succès');
    }

    /**
     * Met à jour une classe
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:classes,id',
            'classe' => 'required|string|max:255',
        ]);

        $classe = Classes::findOrFail($request->id);
        $classe->update(['libelle' => $request->classe]);

        return back()->withStatus('Classe modifiée avec succès');
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes statiques (utilisées dans les vues)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère le libellé du niveau par son ID
     */
    public static function getNiveauDeLaClasse($id_niveau): ?string
    {
        $niveau = Niveau::find($id_niveau);
        return $niveau?->libelle_niveau;
    }

    /**
     * Récupère le niveau à partir de l'ID de la classe
     */
    public static function getNiveauByGivigIdClass($id_classe): ?string
    {
        $classe = Classes::with('niveau')->find($id_classe);
        return $classe?->niveau?->libelle_niveau;
    }

    /**
     * Liste toutes les classes du campus de l'utilisateur
     */
    public static function getListClasse()
    {
        return Classes::with('niveau')
            ->where('campus_id', Auth::user()->campus_id)
            ->orderBy('libelle')
            ->get();
    }
}
