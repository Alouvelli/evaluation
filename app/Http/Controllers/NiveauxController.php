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
    public function getListeNiveaux(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $niveaux = Niveau::withCount('classes')
            ->orderBy('libelle_niveau')
            ->get();

        return view('pages.lists.liste-niveau', compact('niveaux'));
    }

    /**
     * Supprime un niveau
     */
    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        $niveau = Niveau::findOrFail($id);

        // Vérifier qu'aucune classe n'est liée
        if ($niveau->classes()->exists()) {
            return redirect()->route('tools')
                ->with('status', 'Impossible de supprimer ce niveau car il contient une ou plusieurs classes.')
                ->with('redirect_tab', 'niveaux');
        }

        $niveau->delete();

        return redirect()->route('tools')
            ->with('status', 'Niveau supprimé avec succès.')
            ->with('redirect_tab', 'niveaux');
    }

    /**
     * Crée un nouveau niveau
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'niveau' => 'required|string|max:255',
        ], [
            'niveau.required' => 'Le libellé du niveau est obligatoire.',
            'niveau.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
        ]);

        $exists = Niveau::where('libelle_niveau', $request->niveau)->exists();

        if ($exists) {
            return redirect()->route('tools')
                ->with('status', 'Erreur : Ce niveau existe déjà.')
                ->with('redirect_tab', 'niveaux');
        }

        Niveau::create([
            'libelle_niveau' => $request->niveau,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Niveau ajouté avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'niveaux');
    }

    /**
     * Met à jour un niveau
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id_niveau' => 'required|exists:niveaux,id_niveau',
            'niveau' => 'required|string|max:255',
        ], [
            'id_niveau.required' => 'L\'identifiant du niveau est requis.',
            'id_niveau.exists' => 'Ce niveau n\'existe pas.',
            'niveau.required' => 'Le libellé du niveau est obligatoire.',
        ]);

        Niveau::where('id_niveau', $request->id_niveau)
            ->update(['libelle_niveau' => $request->niveau]);

        return redirect()->route('tools')
            ->with('status', 'Niveau modifié avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'niveaux');
    }
}
