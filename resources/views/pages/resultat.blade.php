@extends('layouts.admin')

@section('title', 'Dépouillement - ' . $classe)

@section('content')
    @php
        $tab = [];
        foreach($profs as $p){
            $tab1 = [];
            $moy = 0;
            foreach($questions as $q){
                $pourcent = \App\Http\Controllers\EvaluationsController::getPourcent($p->id_niveau, $classe, $q->idQ, $p->id_professeur, $p->id_cours);
                array_push($tab1, $pourcent);
                $moy += $pourcent;
            }
            array_push($tab, $tab1);
        }
    @endphp

    <!-- Header Info -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $classe }}</h3>
                <p>{{ \App\Http\Controllers\ClassesController::getNiveauDeLaClasse($profs[0]->id_niveau ?? 0) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ count($profs) }}</h3>
                <p>Cours évalués</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ count($questions) }}</h3>
                <p>Questions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <h3>{{ \App\Http\Controllers\EvaluationsController::getTauxDeParticipation($profs[0]->id ?? 0) }}%</h3>
                <p>Participation</p>
            </div>
        </div>
    </div>

    <!-- Questions Modal Button -->
    <div style="margin-bottom: 1rem;">
        <button class="btn btn-secondary" onclick="openModal('modalQuestions')">
            <i class="fas fa-question-circle"></i> Voir les questions
        </button>
        <a href="{{ route('liste_classe', ['annee' => $profs[0]->annee_id ?? 1, 'semestre' => $profs[0]->semestre ?? 1]) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-table"></i> Tableau des résultats</h3>
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <table class="results-table">
                <thead>
                    <tr>
                        <th class="sticky-col">Professeur / Cours</th>
                        @foreach($questions as $q)
                            <th title="{{ $q->libelle }}">Q{{ $q->idQ }}</th>
                        @endforeach
                        <th class="moyenne-col">Moy.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profs as $index => $p)
                        @php
                            $moyProf = count($questions) > 0 ? array_sum($tab[$index]) / count($questions) : 0;
                        @endphp
                        <tr>
                            <td class="sticky-col">
                                <div class="prof-cell">
                                    <strong>{{ $p->full_name }}</strong>
                                    <span>{{ $p->libelle_cours }}</span>
                                </div>
                            </td>
                            @foreach($questions as $qIndex => $q)
                                @php
                                    $pourcent = $tab[$index][$qIndex];
                                    $colorClass = $pourcent < 50 ? 'cell-danger' : ($pourcent <= 80 ? 'cell-success' : 'cell-excellent');
                                @endphp
                                <td class="{{ $colorClass }}">
                                    {{ $pourcent }}%
                                </td>
                            @endforeach
                            <td class="moyenne-col">
                                <span class="moyenne-badge {{ $moyProf < 50 ? 'moy-danger' : ($moyProf <= 75 ? 'moy-warning' : 'moy-success') }}">
                                    {{ round($moyProf) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        <!-- Bar Chart -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Résultats par question</h3>
            </div>
            <div class="card-body">
                <canvas id="barChart" height="300"></canvas>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Taux de participation</h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Comments -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-comments"></i> Commentaires des étudiants</h3>
        </div>
        <div class="card-body">
            @foreach($profs as $p)
                @php
                    $commentaires = \App\Http\Controllers\EvaluationsController::getCommentaire($p->id_professeur, $p->id, $p->id_cours);
                @endphp
                @if($commentaires->count() > 0)
                    <div class="comment-section">
                        <div class="comment-header">
                            <i class="fas fa-user-tie"></i>
                            <strong>{{ $p->full_name }}</strong> - {{ $p->libelle_cours }}
                            <span class="badge badge-info">{{ $commentaires->count() }} commentaire(s)</span>
                        </div>
                        <div class="comments-list">
                            @foreach($commentaires as $c)
                                @if(!empty($c->commentaire))
                                    <div class="comment-item">
                                        <i class="fas fa-quote-left"></i>
                                        <p>{{ $c->commentaire }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if($profs->every(function($p) use ($classe) {
                return \App\Http\Controllers\EvaluationsController::getCommentaire($p->id_professeur, $p->id, $p->id_cours)->count() == 0;
            }))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Aucun commentaire n'a été laissé pour cette classe.
                </div>
            @endif
        </div>
    </div>

    <!-- Questions Modal -->
    <div class="modal-overlay" id="modalQuestions">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h5><i class="fas fa-question-circle"></i> Questions d'évaluation</h5>
                <button class="modal-close" onclick="closeModal('modalQuestions')">&times;</button>
            </div>
            <div class="modal-body">
                @foreach($questions as $q)
                    <div class="question-item">
                        <span class="badge badge-primary">Q{{ $q->idQ }}</span>
                        <span>{{ $q->libelle }}</span>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal('modalQuestions')">Fermer</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .results-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .results-table th,
    .results-table td {
        padding: 0.75rem;
        text-align: center;
        border: 1px solid var(--gray-light);
    }

    .results-table thead th {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        background: var(--primary) !important;
        color: white;
        min-width: 200px;
        text-align: left !important;
        z-index: 11;
    }

    tbody .sticky-col {
        background: white !important;
        color: var(--dark);
    }

    .prof-cell {
        display: flex;
        flex-direction: column;
    }

    .prof-cell strong {
        color: var(--primary);
    }

    .prof-cell span {
        font-size: 0.8rem;
        color: var(--gray);
    }

    .cell-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #991b1b;
        font-weight: 600;
    }

    .cell-success {
        background: rgba(16, 185, 129, 0.2);
        color: #065f46;
        font-weight: 600;
    }

    .cell-excellent {
        background: rgba(16, 185, 129, 0.4);
        color: #065f46;
        font-weight: 700;
    }

    .moyenne-col {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
        color: white !important;
    }

    .moyenne-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 700;
    }

    .moy-danger {
        background: var(--danger);
        color: white;
    }

    .moy-warning {
        background: var(--warning);
        color: white;
    }

    .moy-success {
        background: var(--success);
        color: white;
    }

    .comment-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--gray-light);
    }

    .comment-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }

    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding-left: 1.5rem;
    }

    .comment-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--light);
        border-radius: 8px;
        border-left: 3px solid var(--primary);
    }

    .comment-item i {
        color: var(--primary);
        opacity: 0.5;
    }

    .comment-item p {
        margin: 0;
        color: var(--gray);
        font-style: italic;
    }

    .question-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--gray-light);
    }

    .question-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barData = {
        labels: [
            @foreach($questions as $q)
                'Q{{ $q->idQ }}',
            @endforeach
        ],
        datasets: [
            @foreach($profs as $index => $p)
            {
                label: '{{ $p->full_name }}',
                data: {!! json_encode($tab[$index]) !!},
                backgroundColor: getColor({{ $index }}),
            },
            @endforeach
        ]
    };

    new Chart(barCtx, {
        type: 'bar',
        data: barData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ' + ctx.raw + '%'
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const participation = {{ \App\Http\Controllers\EvaluationsController::getTauxDeParticipation($profs[0]->id ?? 0) }};
    
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Ont évalué', 'N\'ont pas évalué'],
            datasets: [{
                data: [participation, 100 - participation],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.label + ': ' + ctx.raw + '%'
                    }
                }
            }
        }
    });

    function getColor(index) {
        const colors = [
            '#667eea', '#10b981', '#f59e0b', '#ef4444', '#3b82f6',
            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1'
        ];
        return colors[index % colors.length];
    }

    // Modal Functions
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('show');
        });
    });
</script>
@endpush
