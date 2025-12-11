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
    public function getListeClasse($annee, $semestre): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
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
            return back()->with('status', 'Aucun cours trouvé pour cette période.');
        }

        return view('pages.lists.liste-classe', compact('classes'));
    }

    /**
     * Formulaire d'édition d'une classe
     */
    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $classes = Classes::with('niveau')->findOrFail($id);
        $niveaux = Niveau::all();

        return view('pages.edit-classe', compact('classes', 'niveaux'));
    }

    /**
     * Supprime une classe
     */
    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        $classe = Classes::findOrFail($id);

        // Vérifier qu'aucun cours n'est lié
        if ($classe->cours()->exists()) {
            return redirect()->route('tools')
                ->with('status', 'Impossible de supprimer cette classe car elle est associée à un ou plusieurs cours.')
                ->with('redirect_tab', 'classes');
        }

        // Vérifier qu'aucun étudiant n'est lié
        if ($classe->etudiants()->exists()) {
            return redirect()->route('tools')
                ->with('status', 'Impossible de supprimer cette classe car elle contient des étudiants.')
                ->with('redirect_tab', 'classes');
        }

        $classe->delete();

        return redirect()->route('tools')
            ->with('status', 'Classe supprimée avec succès.')
            ->with('redirect_tab', 'classes');
    }

    /**
     * Crée une nouvelle classe
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'classe' => 'required|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id_niveau',
        ], [
            'classe.required' => 'Le libellé de la classe est obligatoire.',
            'classe.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'niveau_id.required' => 'Veuillez sélectionner un niveau.',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas.',
        ]);

        $campusId = Auth::user()->campus_id;

        // Vérifier si la classe existe déjà
        $exists = Classes::where('libelle', $request->classe)
            ->where('campus_id', $campusId)
            ->where('id_niveau', $request->niveau_id)
            ->exists();

        if ($exists) {
            return redirect()->route('tools')
                ->with('status', 'Erreur : Cette classe existe déjà pour ce niveau.')
                ->with('redirect_tab', 'classes');
        }

        Classes::create([
            'libelle' => $request->classe,
            'id_niveau' => $request->niveau_id,
            'campus_id' => $campusId,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Classe ajoutée avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'classes');
    }

    /**
     * Met à jour une classe
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id' => 'required|exists:classes,id',
            'classe' => 'required|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id_niveau',
        ], [
            'id.required' => 'L\'identifiant de la classe est requis.',
            'id.exists' => 'Cette classe n\'existe pas.',
            'classe.required' => 'Le libellé de la classe est obligatoire.',
            'niveau_id.required' => 'Veuillez sélectionner un niveau.',
        ]);

        $classe = Classes::findOrFail($request->id);
        $classe->update([
            'libelle' => $request->classe,
            'id_niveau' => $request->niveau_id,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Classe modifiée avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'classes');
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
    public static function getListClasse(): \Illuminate\Database\Eloquent\Collection
    {
        return Classes::with('niveau')
            ->where('campus_id', Auth::user()->campus_id)
            ->orderBy('libelle')
            ->get();
    }
}
