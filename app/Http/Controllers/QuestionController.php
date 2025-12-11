<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Liste toutes les questions
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $questions = Question::orderBy('idQ')->get();
        return view('pages.questions', compact('questions'));
    }

    /**
     * Crée une nouvelle question
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ], [
            'question.required' => 'Le libellé de la question est obligatoire.',
            'question.max' => 'La question ne peut pas dépasser 500 caractères.',
        ]);

        $exists = Question::where('libelle', $request->question)->exists();

        if ($exists) {
            return redirect()->route('tools')
                ->with('status', 'Erreur : Cette question existe déjà.')
                ->with('redirect_tab', 'questions');
        }

        Question::create([
            'libelle' => $request->question,
        ]);

        return redirect()->route('tools')
            ->with('status', 'Question ajoutée avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'questions');
    }

    /**
     * Supprime une question
     */
    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        $question = Question::where('idQ', $id)->firstOrFail();

        // Vérifier s'il y a des évaluations liées
        if ($question->evaluations()->exists()) {
            return redirect()->route('tools')
                ->with('status', 'Impossible de supprimer cette question car des évaluations y sont associées.')
                ->with('redirect_tab', 'questions');
        }

        $question->delete();

        return redirect()->route('tools')
            ->with('status', 'Question supprimée avec succès.')
            ->with('redirect_tab', 'questions');
    }

    /**
     * Met à jour une question
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'idQ' => 'required|exists:questions,idQ',
            'libelle' => 'required|string|max:500',
        ], [
            'idQ.required' => 'L\'identifiant de la question est requis.',
            'idQ.exists' => 'Cette question n\'existe pas.',
            'libelle.required' => 'Le libellé de la question est obligatoire.',
        ]);

        Question::where('idQ', $request->idQ)
            ->update(['libelle' => $request->libelle]);

        return redirect()->route('tools')
            ->with('status', 'Question modifiée avec succès.')
            ->with('redirect_tab', $request->redirect_tab ?? 'questions');
    }
}
