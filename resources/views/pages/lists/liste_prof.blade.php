@extends('layouts.admin')

@section('title', 'Liste des Enseignants')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Enseignants</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-smile"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['tresSatisfaisant'] }}</h3>
                <p>Très satisfaisant</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-meh"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['satisfaisant'] }}</h3>
                <p>Satisfaisant</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-frown"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['peuSatisfaisant'] }}</h3>
                <p>Peu satisfaisant</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chalkboard-teacher"></i> Liste des Enseignants</h3>
            <span class="badge-count">{{ $profs->count() }} enseignants</span>
        </div>
        <div class="card-body">
            <!-- Légende -->
            <div class="legend-box">
                <div class="legend-title"><i class="fas fa-info-circle"></i> Légende des notes</div>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-color legend-danger"></span>
                        <span class="legend-text">Peu satisfaisant</span>
                        <span class="legend-range">&lt; 65%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color legend-warning"></span>
                        <span class="legend-text">Satisfaisant</span>
                        <span class="legend-range">65% - 85%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color legend-success"></span>
                        <span class="legend-text">Très satisfaisant</span>
                        <span class="legend-range">&gt; 85%</span>
                    </div>
                </div>
            </div>

            @if($profs->count() > 0)
                <!-- Vue Desktop -->
                <div class="table-scroll-wrapper">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="col-fixed">Nom complet</th>
                            <th class="col-note">Note Finale</th>
                            <th class="col-note">Appréciation</th>
                            <th class="col-note">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($profs as $index => $prof)
                            @php
                                $noteFinale = $prof->note_finale;

                                // Seuils: <65 = danger, 65-85 = warning, >85 = success
                                if ($noteFinale < 65) {
                                    $appreciation = 'Peu satisfaisant';
                                    $badgeClass = 'badge-danger';
                                    $noteClass = 'moy-danger';
                                    $rowClass = 'row-danger';
                                } elseif ($noteFinale <= 85) {
                                    $appreciation = 'Satisfaisant';
                                    $badgeClass = 'badge-warning';
                                    $noteClass = 'moy-warning';
                                    $rowClass = 'row-warning';
                                } else {
                                    $appreciation = 'Très satisfaisant';
                                    $badgeClass = 'badge-success';
                                    $noteClass = 'moy-success';
                                    $rowClass = 'row-success';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $index + 1 }}</td>
                                <td class="col-fixed">
                                    <div class="prof-info">
                                        <div class="prof-avatar {{ $noteClass }}">
                                            {{ strtoupper(substr($prof->full_name, 0, 1)) }}
                                        </div>
                                        <strong>{{ $prof->full_name }}</strong>
                                    </div>
                                </td>
                                <td class="col-note">
                                    <span class="moyenne-badge {{ $noteClass }}">{{ $noteFinale }}/100</span>
                                </td>
                                <td class="col-note">
                                    <span class="appreciation-badge {{ $badgeClass }}">
                                        @if($noteFinale >= 85)
                                            <i class="fas fa-check-circle"></i>
                                        @elseif($noteFinale >= 65)
                                            <i class="fas fa-minus-circle"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle"></i>
                                        @endif
                                        {{ $appreciation }}
                                    </span>
                                </td>
                                <td class="col-note">
                                    <div class="action-buttons">
                                        <a href="{{ route('resultat_prof', $prof->id) }}" class="btn-action btn-view" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rapport_prof', $prof->id) }}" class="btn-action btn-pdf" title="Télécharger rapport">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @if($prof->email)
                                            <a href="{{ route('send_rapport_prof', $prof->id) }}"
                                               class="btn-action btn-email"
                                               title="Envoyer par email"
                                               onclick="return confirm('Envoyer le rapport à {{ $prof->email }} ?')">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile Cards -->
                <div class="mobile-cards">
                    @foreach($profs as $prof)
                        @php
                            $noteFinale = $prof->note_finale;

                            if ($noteFinale < 65) {
                                $appreciation = 'Peu satisfaisant';
                                $badgeClass = 'badge-danger';
                                $noteClass = 'moy-danger';
                                $cardBorder = 'card-border-danger';
                            } elseif ($noteFinale <= 85) {
                                $appreciation = 'Satisfaisant';
                                $badgeClass = 'badge-warning';
                                $noteClass = 'moy-warning';
                                $cardBorder = 'card-border-warning';
                            } else {
                                $appreciation = 'Très satisfaisant';
                                $badgeClass = 'badge-success';
                                $noteClass = 'moy-success';
                                $cardBorder = 'card-border-success';
                            }
                        @endphp
                        <div class="prof-card {{ $cardBorder }}">
                            <div class="prof-card-header">
                                <div class="prof-info">
                                    <div class="prof-avatar {{ $noteClass }}">
                                        {{ strtoupper(substr($prof->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4>{{ $prof->full_name }}</h4>
                                        <span class="appreciation-badge {{ $badgeClass }}">
                                            @if($noteFinale >= 85)
                                                <i class="fas fa-check-circle"></i>
                                            @elseif($noteFinale >= 65)
                                                <i class="fas fa-minus-circle"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle"></i>
                                            @endif
                                            {{ $appreciation }}
                                        </span>
                                    </div>
                                </div>
                                <span class="moyenne-badge {{ $noteClass }}">{{ $noteFinale }}/100</span>
                            </div>
                            <div class="prof-card-footer">
                                <a href="{{ route('resultat_prof', $prof->id) }}" class="btn-card">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <a href="{{ route('rapport_prof', $prof->id) }}" class="btn-card btn-card-primary">
                                    <i class="fas fa-file-pdf"></i> Rapport
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun enseignant avec des cours actifs.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        .stat-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .stat-content p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        /* Légende */
        .legend-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .legend-title {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

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
            flex-shrink: 0;
        }

        .legend-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .legend-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .legend-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

        .legend-text {
            font-weight: 500;
            color: #374151;
            font-size: 0.85rem;
        }

        .legend-range {
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        /* Badge & Table */
        .badge-count {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .table-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-results { width: 100%; border-collapse: collapse; min-width: 700px; }
        .table-results th, .table-results td {
            padding: 0.85rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }
        .table-results th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }
        .table-results tbody tr { transition: all 0.2s ease; }
        .table-results tbody tr:hover { background: #f8fafc; }
        .table-results .col-note { text-align: center; }
        .table-results .col-fixed { min-width: 200px; }

        /* Row colors with left border */
        .table-results tbody tr.row-success {
            border-left: 4px solid #10b981;
        }
        .table-results tbody tr.row-warning {
            border-left: 4px solid #f59e0b;
        }
        .table-results tbody tr.row-danger {
            border-left: 4px solid #ef4444;
        }

        /* Prof Info */
        .prof-info { display: flex; align-items: center; gap: 0.75rem; }
        .prof-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .prof-avatar.moy-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .prof-avatar.moy-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .prof-avatar.moy-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        /* Badges */
        .moyenne-badge {
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            color: white;
            display: inline-block;
            font-size: 0.85rem;
        }
        .moy-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .moy-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .moy-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        .appreciation-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: #065f46; }
        .badge-warning { background: rgba(245, 158, 11, 0.15); color: #92400e; }
        .badge-danger { background: rgba(239, 68, 68, 0.15); color: #991b1b; }

        /* Action Buttons */
        .action-buttons { display: flex; gap: 0.5rem; justify-content: center; }
        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .btn-action:hover { transform: translateY(-2px); color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .btn-view { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .btn-pdf { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .btn-email { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

        /* Mobile Cards */
        .mobile-cards { display: none; }
        .prof-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .prof-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        /* Card border colors */
        .card-border-success { border-left: 4px solid #10b981; }
        .card-border-warning { border-left: 4px solid #f59e0b; }
        .card-border-danger { border-left: 4px solid #ef4444; }

        .prof-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1rem;
            gap: 1rem;
        }
        .prof-card-header .prof-info { flex: 1; }
        .prof-card-header h4 { margin: 0 0 0.35rem 0; font-size: 0.95rem; color: #1e293b; }
        .prof-card-footer { display: flex; border-top: 1px solid #e2e8f0; }
        .btn-card {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        .btn-card:hover { background: #f8fafc; color: #1e293b; }
        .btn-card-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-card-primary:hover { opacity: 0.9; color: white; }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 992px) {
            .table-scroll-wrapper { display: none; }
            .mobile-cards { display: block; }
        }

        @media (max-width: 768px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
            .stat-card { padding: 1rem; }
            .stat-icon { width: 42px; height: 42px; font-size: 1rem; }
            .stat-content h3 { font-size: 1.25rem; }

            .legend-items { gap: 1rem; }
            .legend-item { flex-direction: column; align-items: flex-start; gap: 0.25rem; }
        }

        @media (max-width: 480px) {
            .stats-row { grid-template-columns: 1fr; }
            .prof-card-header { flex-direction: column; }
            .prof-card-header .moyenne-badge { align-self: flex-start; margin-top: 0.5rem; }
            .legend-box { padding: 0.75rem 1rem; }
        }
    </style>
@endpush
