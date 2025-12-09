@extends('layouts.admin')

@section('title', 'Résultat - ' . $niveau->libelle_niveau)

@section('content')
    @php
        use App\Http\Controllers\EvaluationsController;
    @endphp

        <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $niveau->libelle_niveau }}</h3>
                <p>Niveau</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $classes->count() }}</h3>
                <p>Classes</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cours->count() }}</h3>
                <p>Cours évalués</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $tauxParticipation }}%</h3>
                <p>Participation</p>
            </div>
        </div>
    </div>

    <!-- Tableau des résultats -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-table"></i> Résultats - {{ $niveau->libelle_niveau }}</h3>
            <a href="{{ route('liste_niveau') }}" class="btn btn-sm btn-back">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Retour</span>
            </a>
        </div>
        <div class="card-body">
            @if($cours->count() > 0)
                <!-- Vue Desktop avec scroll -->
                <div class="table-scroll-wrapper">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th class="col-fixed">Professeur</th>
                            <th>Cours</th>
                            <th>Classe</th>
                            @foreach($questions as $q)
                                <th class="col-note" title="{{ $q->libelle }}">Q{{ $q->idQ }}</th>
                            @endforeach
                            <th class="col-note">Moy.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cours as $c)
                            @php
                                $totalNotes = 0;
                                $countNotes = 0;
                            @endphp
                            <tr>
                                <td class="col-fixed"><strong>{{ $c->professeur->full_name ?? 'N/A' }}</strong></td>
                                <td>{{ $c->libelle_cours }}</td>
                                <td><span class="badge badge-primary">{{ $c->classe->libelle ?? '' }}</span></td>
                                @foreach($questions as $q)
                                    @php
                                        $note = EvaluationsController::getPourcentByCours($c->id_cours, $q->idQ, $c->id_professeur);
                                        $totalNotes += $note;
                                        $countNotes++;

                                        $cellClass = '';
                                        if($note > 0) {
                                            if($note >= 75) $cellClass = 'cell-good';
                                            elseif($note >= 50) $cellClass = 'cell-warning';
                                            else $cellClass = 'cell-danger';
                                        }
                                    @endphp
                                    <td class="col-note {{ $cellClass }}">{{ $note > 0 ? $note . '%' : '-' }}</td>
                                @endforeach
                                @php
                                    $moyenne = $countNotes > 0 ? round($totalNotes / $countNotes) : 0;
                                    $moyClass = $moyenne >= 75 ? 'moy-success' : ($moyenne >= 50 ? 'moy-warning' : 'moy-danger');
                                @endphp
                                <td class="col-note"><span class="moyenne-badge {{ $moyClass }}">{{ $moyenne }}%</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile Cards -->
                <div class="mobile-cards">
                    @foreach($cours as $c)
                        @php
                            $totalNotes = 0;
                            $countNotes = 0;
                            $notesArray = [];
                            foreach($questions as $q) {
                                $note = EvaluationsController::getPourcentByCours($c->id_cours, $q->idQ, $c->id_professeur);
                                $totalNotes += $note;
                                $countNotes++;
                                $notesArray[] = ['q' => $q->idQ, 'note' => $note];
                            }
                            $moyenne = $countNotes > 0 ? round($totalNotes / $countNotes) : 0;
                            $moyClass = $moyenne >= 75 ? 'moy-success' : ($moyenne >= 50 ? 'moy-warning' : 'moy-danger');
                        @endphp
                        <div class="result-card">
                            <div class="result-card-header">
                                <div class="result-info">
                                    <h4>{{ $c->professeur->full_name ?? 'N/A' }}</h4>
                                    <p>{{ $c->libelle_cours }}</p>
                                    <span class="badge badge-primary">{{ $c->classe->libelle ?? '' }}</span>
                                </div>
                                <span class="moyenne-badge {{ $moyClass }}">{{ $moyenne }}%</span>
                            </div>
                            <div class="result-card-body">
                                <div class="notes-grid">
                                    @foreach($notesArray as $n)
                                        @php
                                            $cellClass = '';
                                            if($n['note'] > 0) {
                                                if($n['note'] >= 75) $cellClass = 'cell-good';
                                                elseif($n['note'] >= 50) $cellClass = 'cell-warning';
                                                else $cellClass = 'cell-danger';
                                            }
                                        @endphp
                                        <div class="note-item {{ $cellClass }}">
                                            <span class="note-label">Q{{ $n['q'] }}</span>
                                            <span class="note-value">{{ $n['note'] > 0 ? $n['note'] . '%' : '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun cours actif pour ce niveau.
                </div>
            @endif
        </div>
    </div>
@endsection
