@extends('layouts.admin')

@section('title', 'Résultat - ' . $prof->full_name)

@section('content')
    @php
        use App\Http\Controllers\EvaluationsController;

        if ($noteFinale < 65) {
            $appreciation = 'Peu satisfaisant';
            $appreciationClass = 'danger';
        } elseif ($noteFinale <= 85) {
            $appreciation = 'Satisfaisant';
            $appreciationClass = 'warning';
        } else {
            $appreciation = 'Très satisfaisant';
            $appreciationClass = 'success';
        }
    @endphp

        <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $prof->full_name }}</h3>
                <p>Enseignant</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cours->count() }}</h3>
                <p>Cours</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $noteFinale >= 75 ? 'success' : ($noteFinale >= 50 ? 'warning' : 'danger') }}">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $noteFinale }}/100</h3>
                <p>Note Finale</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $appreciationClass }}">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $appreciation }}</h3>
                <p>Appréciation</p>
            </div>
        </div>
    </div>

    <!-- Tableau des résultats -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-table"></i> Résultats par Cours</h3>
            <div class="header-actions">
                <a href="{{ route('rapport_prof', $prof->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-file-pdf"></i> <span class="btn-text">Rapport PDF</span>
                </a>
                <a href="{{ route('liste_prof') }}" class="btn btn-sm btn-back">
                    <i class="fas fa-arrow-left"></i> <span class="btn-text">Retour</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($cours->count() > 0)
                <!-- Vue Desktop avec scroll -->
                <div class="table-scroll-wrapper">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th class="col-fixed">Cours</th>
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
                                <td class="col-fixed"><strong>{{ $c->libelle_cours }}</strong></td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $c->classe->niveau->libelle_niveau ?? '' }} {{ $c->classe->libelle ?? '' }}
                                    </span>
                                </td>
                                @foreach($questions as $q)
                                    @php
                                        $note = EvaluationsController::getPourcentByCours($c->id_cours, $q->idQ, $prof->id);
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
                                $note = EvaluationsController::getPourcentByCours($c->id_cours, $q->idQ, $prof->id);
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
                                    <h4>{{ $c->libelle_cours }}</h4>
                                    <span class="badge badge-primary">{{ $c->classe->niveau->libelle_niveau ?? '' }} {{ $c->classe->libelle ?? '' }}</span>
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
                    <i class="fas fa-info-circle"></i> Aucun cours actif pour cet enseignant.
                </div>
            @endif
        </div>
    </div>

    <!-- Graphique -->
    <div class="card mt-4">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Performance par Question</h3>
        </div>
        <div class="card-body">
            <canvas id="chartQuestions" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartQuestions').getContext('2d');

            const questionLabels = [@foreach($questions as $q)'Q{{ $q->idQ }}',@endforeach];

            const moyennesParQuestion = [
                @foreach($questions as $q)
                    @php
                        $total = 0;
                        $count = 0;
                        foreach($cours as $c) {
                            $note = EvaluationsController::getPourcentByCours($c->id_cours, $q->idQ, $prof->id);
                            if ($note > 0) {
                                $total += $note;
                                $count++;
                            }
                        }
                        $moyQ = $count > 0 ? round($total / $count) : 0;
                    @endphp
                    {{ $moyQ }},
                @endforeach
            ];

            const colors = moyennesParQuestion.map(m => {
                if (m >= 75) return 'rgba(16, 185, 129, 0.8)';
                if (m >= 50) return 'rgba(245, 158, 11, 0.8)';
                return 'rgba(239, 68, 68, 0.8)';
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: questionLabels,
                    datasets: [{
                        label: 'Moyenne (%)',
                        data: moyennesParQuestion,
                        backgroundColor: colors,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { callback: function(value) { return value + '%'; } } }
                    }
                }
            });
        });
    </script>
@endpush
