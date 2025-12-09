@extends('layouts.admin')

@section('title', 'Liste des Niveaux')

@section('content')
    @php
        use App\Http\Controllers\EvaluationsController;
    @endphp

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-layer-group"></i> Résultats par Niveau</h3>
        </div>
        <div class="card-body">
            @if($niveaux->count() > 0)
                <div class="niveau-grid">
                    @foreach($niveaux as $niveau)
                        @php
                            $tauxParticipation = EvaluationsController::getTauxDeParticipationNiveau($niveau->id_niveau);
                        @endphp
                        <a href="{{ route('resultat_niveau', $niveau->id_niveau) }}" class="niveau-card">
                            <div class="niveau-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="niveau-content">
                                <h4>{{ $niveau->libelle_niveau }}</h4>
                                <div class="niveau-stats">
                                    <span class="stat">
                                        <i class="fas fa-chart-pie"></i>
                                        {{ $tauxParticipation }}% participation
                                    </span>
                                </div>
                            </div>
                            <div class="niveau-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun niveau disponible.
                </div>
            @endif
        </div>
    </div>
@endsection
