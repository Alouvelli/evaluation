<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EtudiantsImportController;
use App\Http\Controllers\EvaluationsController;
use App\Http\Controllers\NiveauxController;
use App\Http\Controllers\ProfesseursController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Routes publiques (Évaluation étudiants)
|--------------------------------------------------------------------------
*/

Route::get('/', [EvaluationsController::class, 'index'])->name('evaluation');
Route::get('/check-matricule', fn() => redirect()->route('evaluation'));
Route::post('/check-matricule', [EvaluationsController::class, 'checkMatricule'])->name('check-matricule');
Route::post('/evaluation/store', [EvaluationsController::class, 'store'])->name('evaluation.store');
Route::get('/evaluation/success', [EvaluationsController::class, 'success'])->name('evaluation.success');

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Routes protégées (Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard & Outils
    |--------------------------------------------------------------------------
    */
    Route::get('/tools', [ProfesseursController::class, 'getAll'])->name('tools');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Professeurs
    |--------------------------------------------------------------------------
    */
    Route::prefix('professeurs')->name('professeurs.')->group(function () {
        Route::post('/store', [ProfesseursController::class, 'store'])->name('store');
        Route::post('/modify', [ProfesseursController::class, 'modify'])->name('modify');
        Route::get('/delete/{id}', [ProfesseursController::class, 'delete'])->name('delete');
        Route::get('/edit/{id}', [ProfesseursController::class, 'edit'])->name('edit');
    });

    // Routes legacy (pour compatibilité avec les vues existantes)
    Route::post('/new-professeur', [ProfesseursController::class, 'store'])->name('newProfesseur');
    Route::post('/modify-professeur', [ProfesseursController::class, 'modify'])->name('modifyProfesseur');
    Route::get('/delete-professeur/{id}', [ProfesseursController::class, 'delete'])->name('deleteProfesseur');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Cours
    |--------------------------------------------------------------------------
    */
    Route::prefix('cours')->name('cours.')->group(function () {
        Route::post('/store', [CoursController::class, 'store'])->name('store');
        Route::post('/update', [CoursController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [CoursController::class, 'delete'])->name('delete');
        Route::get('/modify/{id}', [CoursController::class, 'modify'])->name('modify');
    });

    // Routes legacy
    Route::post('/new-cours', [CoursController::class, 'store'])->name('newCours');
    Route::post('/update-cours', [CoursController::class, 'update'])->name('update_cours');
    Route::get('/delete-cours/{id}', [CoursController::class, 'delete'])->name('deleteCours');
    Route::get('/modify-cours/{id}', [CoursController::class, 'modify'])->name('modifyCours');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Classes
    |--------------------------------------------------------------------------
    */
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::post('/store', [ClassesController::class, 'store'])->name('store');
        Route::post('/update', [ClassesController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [ClassesController::class, 'delete'])->name('delete');
        Route::get('/edit/{id}', [ClassesController::class, 'edit'])->name('edit');
        Route::get('/liste/{annee}/{semestre}', [ClassesController::class, 'getListeClasse'])->name('liste');
    });

    // Routes legacy
    Route::post('/new-classe', [ClassesController::class, 'store'])->name('newClasse');
    Route::post('/update-classe', [ClassesController::class, 'update'])->name('updateClasse');
    Route::get('/delete-classe/{id}', [ClassesController::class, 'delete'])->name('deleteClasse');
    Route::get('/liste-classe/{annee}/{semestre}', [ClassesController::class, 'getListeClasse'])->name('liste_classe');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Niveaux
    |--------------------------------------------------------------------------
    */
    Route::prefix('niveaux')->name('niveaux.')->group(function () {
        Route::get('/', [NiveauxController::class, 'getListeNiveaux'])->name('index');
        Route::post('/store', [NiveauxController::class, 'store'])->name('store');
        Route::post('/update', [NiveauxController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [NiveauxController::class, 'delete'])->name('delete');
    });

    // Routes legacy
    Route::get('/liste-niveau', [EvaluationsController::class, 'getListeNiveau'])->name('liste_niveau');
    Route::post('/new-niveau', [NiveauxController::class, 'store'])->name('newNiveau');
    Route::post('/update-niveau', [NiveauxController::class, 'update'])->name('updateNiveau');
    Route::get('/delete-niveau/{id}', [NiveauxController::class, 'delete'])->name('deleteNiveau');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Questions
    |--------------------------------------------------------------------------
    */
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::post('/store', [QuestionController::class, 'store'])->name('store');
        Route::post('/update', [QuestionController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [QuestionController::class, 'delete'])->name('delete');
    });

    // Routes legacy
    Route::post('/new-question', [QuestionController::class, 'store'])->name('newQuestion');
    Route::post('/modify-question', [QuestionController::class, 'update'])->name('modifyQuestion');
    Route::get('/delete-question/{id}', [QuestionController::class, 'delete'])->name('deleteQuestion');

    /*
    |--------------------------------------------------------------------------
    | Import des Étudiants
    |--------------------------------------------------------------------------
    */
    Route::prefix('etudiants')->name('etudiants.')->group(function () {
        Route::get('/import', [EtudiantsImportController::class, 'index'])->name('import');
        Route::post('/import', [EtudiantsImportController::class, 'store'])->name('import.store');
        Route::get('/classe/{id}', [EtudiantsImportController::class, 'showByClasse'])->name('byClasse');
        Route::post('/reset-statut', [EtudiantsImportController::class, 'resetStatut'])->name('resetStatut');
        Route::delete('/classe', [EtudiantsImportController::class, 'destroyByClasse'])->name('destroyByClasse');
    });

    // Route legacy
    Route::post('/import-etudiants', [EtudiantsImportController::class, 'store'])->name('import_etudiants');

    /*
    |--------------------------------------------------------------------------
    | Activation des Évaluations
    |--------------------------------------------------------------------------
    */
    Route::get('/activation', [CoursController::class, 'activationCours'])->name('activation');
    Route::post('/change-evaluation-active', [CoursController::class, 'changeEvaluationActive'])->name('change_evaluation_active');

    /*
    |--------------------------------------------------------------------------
    | Résultats & Rapports
    |--------------------------------------------------------------------------
    */

    Route::get('/enseignants', [EvaluationsController::class, 'getEnseignant'])->name('liste_prof');
    Route::get('/rapport/{id}', [ProfesseursController::class, 'getRapport'])->name('rapport');
    Route::get('/rapport-prof/{id}', [ProfesseursController::class, 'getRapport'])->name('rapport_prof');


    Route::get('/resultat-prof/{id}', [EvaluationsController::class, 'getResultatProf'])->name('resultat_prof');
    Route::get('/api/resultat-classe/{classeId}', [EvaluationsController::class, 'getResultatClasse'])->middleware('auth');
    Route::get('/resultat/{classe}/{annee_id}/{semestre}/{id_niveau}/{campus_id}', [EvaluationsController::class, 'getResultat'])->name('resultat');
    Route::get('/resultat-niveau/{id_niveau}', [EvaluationsController::class, 'getResultatNiveau'])->name('resultat_niveau');
    Route::get('/resultat-general', [EvaluationsController::class, 'getResultatGeneral'])->name('resultat_general');

    /*
    |--------------------------------------------------------------------------
    | Gestion des Utilisateurs (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'getAllUser'])->name('users');
        Route::get('/activer/{id}', [UserController::class, 'activerUser'])->name('activer');
        Route::get('/desactiver/{id}', [UserController::class, 'desactiverUser'])->name('desactiver');
        Route::get('/delete/{id}', [UserController::class, 'deleteUser'])->name('delete');
        Route::get('/define-admin/{id}', [UserController::class, 'defineAdmin'])->name('defineAdmin');
    });

    // Routes legacy
    Route::get('/admin', [UserController::class, 'getAllUser'])->name('admin');
    Route::get('/activer-user/{id}', [UserController::class, 'activerUser'])->name('activerUser');
    Route::get('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
    Route::get('/define-admin/{id}', [UserController::class, 'defineAdmin'])->name('defineAdmin');
});
