@extends('layouts.admin')

@section('title', 'Liste des Classes')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Liste des Classes</h3>
            <span class="badge-count">
                {{ $classes[0]->annee1 ?? '' }} - {{ $classes[0]->annee2 ?? '' }} | Semestre {{ $classes[0]->semestre ?? '' }}
            </span>
        </div>
        <div class="card-body">
            <div class="table-scroll-wrapper">
                <table class="table-results">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Taux Participation</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($classes as $item)
                        @php
                            $tauxParticipation = \App\Http\Controllers\EvaluationsController::getTauxDeParticipation($item->id);
                            $progressClass = $tauxParticipation < 50 ? 'danger' : ($tauxParticipation < 75 ? 'warning' : 'success');
                        @endphp
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td><strong>{{ $item->libelle }}</strong></td>
                            <td>
                                    <span class="badge-primary">
                                        {{ \App\Http\Controllers\ClassesController::getNiveauDeLaClasse($item->id_niveau) }}
                                    </span>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <div class="progress-bar-mini">
                                        <div class="progress-fill {{ $progressClass }}" style="width: {{ $tauxParticipation }}%;"></div>
                                    </div>
                                    <span class="progress-value">{{ $tauxParticipation }}%</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('resultat', [
                                        'classe' => $item->libelle,
                                        'annee_id' => $item->annee_id,
                                        'semestre' => $item->semestre,
                                        'id_niveau' => $item->id_niveau,
                                        'campus_id' => $item->campus_id
                                    ]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-bar"></i> <span class="btn-text">Dépouillement</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
