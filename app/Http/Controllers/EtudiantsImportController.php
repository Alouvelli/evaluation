<?php

namespace App\Http\Controllers;

use App\Imports\EtudiantImport;
use App\Models\Classes;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EtudiantsImportController extends Controller
{
    /**
     * Affiche la page d'import avec la liste des classes
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $classes = Classes::with('niveau')
            ->where('campus_id', Auth::user()->campus_id)
            ->orderBy('libelle')
            ->get();

        return view('pages.import-etudiants', compact('classes'));
    }

    /**
     * Importe les étudiants depuis un fichier Excel/CSV
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'classe' => 'required|integer|exists:classes,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ], [
            'classe.required' => 'Veuillez sélectionner une classe',
            'classe.exists' => 'La classe sélectionnée n\'existe pas',
            'file.required' => 'Veuillez sélectionner un fichier',
            'file.mimes' => 'Le fichier doit être au format Excel (xlsx, xls) ou CSV',
            'file.max' => 'Le fichier ne doit pas dépasser 5 Mo',
        ]);

        $classeId = $request->classe;
        $campusId = Auth::user()->campus_id;

        // Vérifier que la classe appartient au campus de l'utilisateur
        $classe = Classes::where('id', $classeId)
            ->where('campus_id', $campusId)
            ->first();

        if (!$classe) {
            return back()->withStatus('Cette classe n\'appartient pas à votre campus');
        }

        try {
            // Option: réinitialiser le statut des étudiants existants au lieu de les supprimer
            Etudiant::where('id_classe', $classeId)
                ->where('campus_id', $campusId)
                ->update(['statut' => Etudiant::STATUT_INACTIF]);

            // Importer le fichier
            $import = new EtudiantImport($classeId, $campusId);
            Excel::import($import, $request->file('file'));

            $importCount = $import->getImportCount();
            $skipCount = $import->getSkipCount();

            $message = "Importation réussie ! {$importCount} étudiant(s) ajouté(s)";
            if ($skipCount > 0) {
                $message .= ", {$skipCount} mis à jour ou ignoré(s)";
            }

            return back()->withStatus($message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Ligne {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return back()->withStatus('Erreurs de validation: ' . implode('; ', array_slice($errorMessages, 0, 5)));

        } catch (\Exception $e) {
            return back()->withStatus('Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Supprime tous les étudiants d'une classe
     */
    public function destroyByClasse(Request $request)
    {
        $request->validate([
            'classe' => 'required|integer|exists:classes,id',
        ]);

        $classeId = $request->classe;
        $campusId = Auth::user()->campus_id;

        // Vérifier que la classe appartient au campus
        $classe = Classes::where('id', $classeId)
            ->where('campus_id', $campusId)
            ->first();

        if (!$classe) {
            return back()->withStatus('Cette classe n\'appartient pas à votre campus');
        }

        $count = Etudiant::where('id_classe', $classeId)
            ->where('campus_id', $campusId)
            ->delete();

        return back()->withStatus("{$count} étudiant(s) supprimé(s) de la classe {$classe->libelle}");
    }

    /**
     * Liste les étudiants d'une classe
     */
    public function showByClasse($classeId)
    {
        $campusId = Auth::user()->campus_id;

        $classe = Classes::where('id', $classeId)
            ->where('campus_id', $campusId)
            ->firstOrFail();

        $etudiants = Etudiant::where('id_classe', $classeId)
            ->where('campus_id', $campusId)
            ->orderBy('matricule')
            ->get();

        return view('pages.liste-etudiants', compact('classe', 'etudiants'));
    }

    /**
     * Réinitialise le statut de tous les étudiants d'une classe
     */
    public function resetStatut(Request $request)
    {
        $request->validate([
            'classe' => 'required|integer|exists:classes,id',
        ]);

        $classeId = $request->classe;
        $campusId = Auth::user()->campus_id;

        $count = Etudiant::where('id_classe', $classeId)
            ->where('campus_id', $campusId)
            ->update(['statut' => Etudiant::STATUT_INACTIF]);

        return back()->withStatus("{$count} étudiant(s) peuvent à nouveau évaluer");
    }
}
