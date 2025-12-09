<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Niveau;
use Illuminate\Http\Request;

class NiveauxController extends Controller
{
    /**
     * Affiche la liste des niveaux
     */
    public function getListeNiveaux()
    {
        $niveaux = Niveau::withCount('classes')
            ->orderBy('libelle_niveau')
            ->get();

        return view('pages.lists.liste-niveau', compact('niveaux'));
    }

    /**
     * Supprime un niveau
     */
    public function delete($id)
    {
        $niveau = Niveau::findOrFail($id);

        // Vérifier qu'aucune classe n'est liée
        if ($niveau->classes()->exists()) {
            return back()->withStatus('Impossible, ce niveau contient une classe !');
        }

        $niveau->delete();

        return back()->withStatus('Niveau supprimé avec succès');
    }

    /**
     * Crée un nouveau niveau
     */
    public function store(Request $request)
    {
        $request->validate([
            'niveau' => 'required|string|max:255',
        ]);

        $exists = Niveau::where('libelle_niveau', $request->niveau)->exists();

        if ($exists) {
            return back()->withStatus('Ce niveau existe déjà');
        }

        Niveau::create([
            'libelle_niveau' => $request->niveau,
        ]);

        return back()->withStatus('Niveau ajouté avec succès');
    }

    /**
     * Met à jour un niveau
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_niveau' => 'required|exists:niveaux,id_niveau',
            'niveau' => 'required|string|max:255',
        ]);

        Niveau::where('id_niveau', $request->id_niveau)
            ->update(['libelle_niveau' => $request->niveau]);

        return back()->withStatus('Niveau modifié avec succès');
    }
}
