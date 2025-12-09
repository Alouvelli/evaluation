<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Liste toutes les questions
     */
    public function index()
    {
        $questions = Question::orderBy('idQ')->get();
        return view('pages.questions', compact('questions'));
    }

    /**
     * Crée une nouvelle question
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $exists = Question::where('libelle', $request->question)->exists();

        if ($exists) {
            return back()->withStatus('Cette question existe déjà');
        }

        Question::create([
            'libelle' => $request->question,
        ]);

        return back()->withStatus('Question ajoutée avec succès');
    }

    /**
     * Supprime une question
     */
    public function delete($id)
    {
        $question = Question::where('idQ', $id)->firstOrFail();

        // Vérifier s'il y a des évaluations liées
        if ($question->evaluations()->exists()) {
            return back()->withStatus('Impossible de supprimer cette question, des évaluations y sont liées !');
        }

        $question->delete();

        return back()->withStatus('Question supprimée avec succès');
    }

    /**
     * Met à jour une question
     */
    public function update(Request $request)
    {
        $request->validate([
            'idQ' => 'required|exists:questions,idQ',
            'libelle' => 'required|string|max:500',
        ]);

        Question::where('idQ', $request->idQ)
            ->update(['libelle' => $request->libelle]);

        return back()->withStatus('Question modifiée avec succès');
    }
}
