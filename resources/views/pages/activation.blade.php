@php use App\Http\Controllers\CoursController; @endphp
@extends('layouts.admin')

@section('title', 'Activation des Évaluations')

@section('content')
    <!-- Current Evaluation Status -->
    <div class="stats-row" style="grid-template-columns: 1fr;">
        <div class="stat-card stat-card-highlight">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <p>Évaluation en cours</p>
                <h3>{{ CoursController::getEvaluationActive() }}</h3>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-toggle-on"></i> Changer l'évaluation active</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Attention :</strong> Le changement de l'évaluation en cours implique sa clôture.
                    Les étudiants évalueront désormais sur l'année et le semestre définis.
                    Les statuts des étudiants seront réinitialisés.
                </div>
            </div>

            <form method="POST" action="{{ route('change_evaluation_active') }}" class="form-modern">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar"></i> Année académique <span class="required">*</span>
                    </label>
                    <select class="form-select" name="annee" required>
                        <option value="">-- Sélectionner l'année académique --</option>
                        @foreach($an as $a)
                            <option value="{{ $a->id }}">{{ $a->annee1 }} - {{ $a->annee2 }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock"></i> Semestre <span class="required">*</span>
                    </label>
                    <div class="radio-cards">
                        <label class="radio-card">
                            <input type="radio" name="semestre" value="1" required>
                            <div class="radio-card-content">
                                <i class="fas fa-1"></i>
                                <span>Semestre 1</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="semestre" value="2">
                            <div class="radio-card-content">
                                <i class="fas fa-2"></i>
                                <span>Semestre 2</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Valider le changement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bolt"></i> Actions rapides</h3>
        </div>
        <div class="card-body">
            <div class="action-grid">
                <a href="{{ route('etudiants.import') }}" class="action-card">
                    <div class="action-icon success">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="action-content">
                        <h4>Importer Étudiants</h4>
                        <p>Importer la liste des étudiants par classe</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('tools') }}" class="action-card">
                    <div class="action-icon info">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gérer les Cours</h4>
                        <p>Ajouter ou modifier les cours</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('liste_prof') }}" class="action-card">
                    <div class="action-icon warning">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="action-content">
                        <h4>Rapports</h4>
                        <p>Générer les rapports enseignants</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
