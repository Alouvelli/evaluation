@extends('layouts.admin')

@section('title', 'Étudiants - ' . ($classe->libelle ?? ''))

@section('content')
    @php
        $evalues = $etudiants->where('statut', 2)->count();
        $nonEvalues = $etudiants->where('statut', 0)->count();
        $total = $etudiants->count();
        $tauxParticipation = $total > 0 ? round(($evalues / $total) * 100) : 0;
    @endphp

        <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $total }}</h3>
                <p>Total étudiants</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $evalues }}</h3>
                <p>Ont évalué</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $nonEvalues }}</h3>
                <p>En attente</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $tauxParticipation }}%</h3>
                <p>Participation</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fas fa-users"></i>
                {{ $classe->niveau->libelle_niveau ?? '' }} - {{ $classe->libelle }}
            </h3>
            <a href="{{ route('etudiants.import') }}" class="btn btn-sm btn-back">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Retour</span>
            </a>
        </div>
        <div class="card-body">
            <!-- Actions -->
            @if($total > 0)
                <div class="actions-bar">
                    <form action="{{ route('etudiants.resetStatut') }}" method="POST"
                          onsubmit="return confirm('Réinitialiser le statut de tous les étudiants de cette classe ?')">
                        @csrf
                        <input type="hidden" name="classe" value="{{ $classe->id }}">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-redo"></i> Réinitialiser les statuts
                        </button>
                    </form>

                    <form action="{{ route('etudiants.destroyByClasse') }}" method="POST"
                          onsubmit="return confirm('ATTENTION: Supprimer TOUS les étudiants de cette classe ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer tous
                        </button>
                    </form>
                </div>
            @endif

            <!-- Table -->
            @if($total > 0)
                <div class="table-scroll-wrapper">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Matricule</th>
                            <th>Statut</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($etudiants as $index => $etudiant)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <code class="matricule-code">{{ $etudiant->matricule }}</code>
                                </td>
                                <td>
                                    @if($etudiant->statut == 0)
                                        <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i> En attente
                                            </span>
                                    @else
                                        <span class="status-badge status-active">
                                                <i class="fas fa-check"></i> A évalué
                                            </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        Aucun étudiant dans cette classe.
                        <a href="{{ route('etudiants.import') }}" class="alert-link">
                            Importer des étudiants
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
