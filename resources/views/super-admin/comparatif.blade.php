@extends('layouts.admin')

@section('title', 'Comparatif Campus')

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-balance-scale"></i> Comparatif des Campus</h1>
            <p class="subtitle">Analyse comparative des performances entre campus</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Table Comparative -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Tableau Comparatif</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-compare">
                    <thead>
                    <tr>
                        <th>Campus</th>
                        <th class="text-center">Moyenne Générale</th>
                        <th class="text-center">Taux Participation</th>
                        <th class="text-center">Professeurs</th>
                        <th class="text-center">
                            <span class="badge badge-success">Très satisfaisant</span>
                        </th>
                        <th class="text-center">
                            <span class="badge badge-warning">Satisfaisant</span>
                        </th>
                        <th class="text-center">
                            <span class="badge badge-danger">Peu satisfaisant</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($comparatifData as $data)
                        <tr>
                            <td>
                                <strong><i class="fas fa-building text-primary"></i> {{ $data['campus']->nomCampus }}</strong>
                            </td>
                            <td class="text-center">
                                @php
                                    $moy = $data['moyenneGenerale'];
                                    $moyClass = $moy > 85 ? 'badge-success' : ($moy >= 65 ? 'badge-warning' : 'badge-danger');
                                @endphp
                                <span class="badge badge-lg {{ $moyClass }}">{{ $moy }}/100</span>
                            </td>
                            <td class="text-center">
                                <div class="progress-cell">
                                    <div class="mini-progress">
                                        <div class="mini-progress-bar" style="width: {{ $data['tauxParticipation'] }}%"></div>
                                    </div>
                                    <span>{{ $data['tauxParticipation'] }}%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <strong>{{ $data['totalProfs'] }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="count-badge count-success">{{ $data['tresSatisfaisant'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="count-badge count-warning">{{ $data['satisfaisant'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="count-badge count-danger">{{ $data['peuSatisfaisant'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="charts-grid">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Moyennes par Campus</h3>
            </div>
            <div class="card-body">
                <canvas id="chartMoyennes" height="300"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> Taux de Participation</h3>
            </div>
            <div class="card-body">
                <canvas id="chartParticipation" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Légende -->
    <div class="card">
        <div class="card-body">
            <div class="legend-box">
                <h4><i class="fas fa-info-circle"></i> Légende des Notes</h4>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-color bg-success"></span>
                        <span>Très satisfaisant : &gt; 85%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-warning"></span>
                        <span>Satisfaisant : 65% - 85%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-danger"></span>
                        <span>Peu satisfaisant : &lt; 65%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h1 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .page-header h1 i { color: #667eea; }
        .header-actions { display: flex; gap: 0.5rem; }

        .table-compare th, .table-compare td {
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-primary { color: #667eea; }

        .badge-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .badge-success { background: rgba(16,185,129,0.15); color: #059669; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #d97706; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #dc2626; }

        .progress-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: center;
        }
        .mini-progress {
            width: 100px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .mini-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .count-success { background: rgba(16,185,129,0.15); color: #059669; }
        .count-warning { background: rgba(245,158,11,0.15); color: #d97706; }
        .count-danger { background: rgba(239,68,68,0.15); color: #dc2626; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        @media (max-width: 992px) { .charts-grid { grid-template-columns: 1fr; } }

        .legend-box h4 {
            margin: 0 0 1rem;
            color: #374151;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .legend-box h4 i { color: #667eea; }
        .legend-items {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }
        .bg-success { background: #10b981; }
        .bg-warning { background: #f59e0b; }
        .bg-danger { background: #ef4444; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données pour les graphiques
        const campusLabels = @json($comparatifData->pluck('campusName'));
        const moyennesData = @json($comparatifData->pluck('moyenneGenerale'));
        const participationData = @json($comparatifData->pluck('tauxParticipation'));

        // Couleurs
        const colors = [
            '#667eea', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#ec4899'
        ];

        // Chart Moyennes
        new Chart(document.getElementById('chartMoyennes'), {
            type: 'bar',
            data: {
                labels: campusLabels,
                datasets: [{
                    label: 'Moyenne Générale',
                    data: moyennesData,
                    backgroundColor: colors.slice(0, campusLabels.length),
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });

        // Chart Participation
        new Chart(document.getElementById('chartParticipation'), {
            type: 'doughnut',
            data: {
                labels: campusLabels,
                datasets: [{
                    data: participationData,
                    backgroundColor: colors.slice(0, campusLabels.length),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
