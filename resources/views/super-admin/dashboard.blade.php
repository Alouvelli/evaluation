@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')

@section('content')
    <!-- Header avec titre -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-crown"></i> Dashboard Super Admin</h1>
            <p class="subtitle">Vue globale de tous les campus</p>
        </div>
    </div>

    <!-- Stats Globales -->
    <div class="section-title">
        <h3><i class="fas fa-globe"></i> Statistiques Globales</h3>
    </div>
    <div class="stats-grid-7">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-building"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalCampus'] }}</h3>
                <p>Campus</p>
            </div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalProfesseurs'] }}</h3>
                <p>Professeurs</p>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalEtudiants'] }}</h3>
                <p>Étudiants</p>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalCours'] }}</h3>
                <p>Cours</p>
            </div>
        </div>
        <div class="stat-card stat-cyan">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalClasses'] }}</h3>
                <p>Classes</p>
            </div>
        </div>
        <div class="stat-card stat-pink">
            <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalEvaluations'] }}</h3>
                <p>Évaluations</p>
            </div>
        </div>
        <div class="stat-card stat-indigo">
            <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            <div class="stat-content">
                <h3>{{ $globalStats['totalUsers'] }}</h3>
                <p>Utilisateurs</p>
            </div>
        </div>
    </div>

    <!-- Statistiques par Campus -->
    <div class="section-title">
        <h3><i class="fas fa-chart-bar"></i> Statistiques par Campus</h3>
    </div>
    <div class="campus-grid">
        @foreach($campusStats as $stat)
            <div class="campus-card">
                <div class="campus-header">
                    <h4><i class="fas fa-building"></i> {{ $stat['campus']->nomCampus }}</h4>
                    <form action="{{ route('super-admin.switch-campus') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="campus_id" value="{{ $stat['campus']->id }}">
                        <button type="submit" class="btn btn-sm btn-outline" title="Voir ce campus">
                            <i class="fas fa-eye"></i> Accéder
                        </button>
                    </form>
                </div>
                <div class="campus-stats">
                    <div class="campus-stat">
                        <span class="stat-value">{{ $stat['coursActifs'] }}</span>
                        <span class="stat-label">Cours actifs</span>
                    </div>
                    <div class="campus-stat">
                        <span class="stat-value">{{ $stat['etudiantsEvalues'] }}/{{ $stat['totalEtudiants'] }}</span>
                        <span class="stat-label">Étudiants évalués</span>
                    </div>
                    <div class="campus-stat">
                    <span class="stat-value {{ $stat['tauxParticipation'] >= 70 ? 'text-success' : ($stat['tauxParticipation'] >= 40 ? 'text-warning' : 'text-danger') }}">
                        {{ $stat['tauxParticipation'] }}%
                    </span>
                        <span class="stat-label">Participation</span>
                    </div>
                    <div class="campus-stat">
                        @php
                            $moy = $stat['moyenneGenerale'];
                            $moyClass = $moy > 85 ? 'text-success' : ($moy >= 65 ? 'text-warning' : 'text-danger');
                        @endphp
                        <span class="stat-value {{ $moyClass }}">{{ $moy }}/100</span>
                        <span class="stat-label">Moyenne générale</span>
                    </div>
                </div>
                <div class="campus-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stat['tauxParticipation'] }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Top Professeurs -->
    <div class="row-2">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-trophy"></i> Top 5 Professeurs (Global)</h3>
            </div>
            <div class="card-body">
                @if($topProfesseurs->count() > 0)
                    <div class="top-list">
                        @foreach($topProfesseurs as $index => $prof)
                            <div class="top-item">
                                <div class="top-rank rank-{{ $index + 1 }}">
                                    @if($index == 0)
                                        <i class="fas fa-crown"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div class="top-info">
                                    <strong>{{ $prof->full_name }}</strong>
                                    <small>{{ $prof->nomCampus }}</small>
                                </div>
                                <div class="top-score">
                            <span class="badge {{ $prof->moyenne > 85 ? 'badge-success' : 'badge-warning' }}">
                                {{ $prof->moyenne }}/100
                            </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucune évaluation disponible</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-link"></i> Accès Rapide</h3>
            </div>
            <div class="card-body">
                <div class="quick-links">
                    <a href="{{ route('super-admin.users') }}" class="quick-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Gérer les utilisateurs</span>
                    </a>
                    <a href="{{ route('super-admin.campuses') }}" class="quick-link">
                        <i class="fas fa-building"></i>
                        <span>Gérer les campus</span>
                    </a>
                    <a href="{{ route('super-admin.comparatif') }}" class="quick-link">
                        <i class="fas fa-balance-scale"></i>
                        <span>Comparatif campus</span>
                    </a>
                    <form action="{{ route('super-admin.switch-campus') }}" method="POST" class="quick-link-form">
                        @csrf
                        <input type="hidden" name="campus_id" value="all">
                        <button type="submit" class="quick-link">
                            <i class="fas fa-globe"></i>
                            <span>Vue globale</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            margin: 0;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .page-header h1 i {
            color: #f59e0b;
        }
        .page-header .subtitle {
            margin: 0.5rem 0 0;
            color: #64748b;
        }

        .section-title {
            margin: 2rem 0 1rem;
        }
        .section-title h3 {
            color: #374151;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title h3 i {
            color: #667eea;
        }

        /* Stats Grid 7 colonnes */
        .stats-grid-7 {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1400px) { .stats-grid-7 { grid-template-columns: repeat(4, 1fr); } }
        @media (max-width: 992px) { .stats-grid-7 { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .stats-grid-7 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .stats-grid-7 { grid-template-columns: 1fr; } }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }
        .stat-purple .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-blue .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-green .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-orange .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-cyan .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .stat-pink .stat-icon { background: linear-gradient(135deg, #ec4899, #db2777); }
        .stat-indigo .stat-icon { background: linear-gradient(135deg, #6366f1, #4f46e5); }

        .stat-content h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #1e293b;
        }
        .stat-content p {
            margin: 0;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Campus Grid */
        .campus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .campus-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .campus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .campus-header h4 {
            margin: 0;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .campus-header h4 i {
            color: #667eea;
        }

        .campus-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .campus-stat {
            text-align: center;
        }
        .campus-stat .stat-value {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }
        .campus-stat .stat-label {
            font-size: 0.75rem;
            color: #64748b;
        }
        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-danger { color: #ef4444 !important; }

        .campus-progress {
            margin-top: 1rem;
        }
        .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        /* Row 2 colonnes */
        .row-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        @media (max-width: 992px) { .row-2 { grid-template-columns: 1fr; } }

        /* Top list */
        .top-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .top-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
        }
        .top-rank {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 0.9rem;
        }
        .rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #cd7c32, #b8860b); }
        .rank-4, .rank-5 { background: #e2e8f0; color: #64748b; }

        .top-info {
            flex: 1;
        }
        .top-info strong {
            display: block;
            color: #1e293b;
        }
        .top-info small {
            color: #64748b;
        }

        /* Quick links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem;
            background: #f8fafc;
            border-radius: 12px;
            text-decoration: none;
            color: #374151;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .quick-link:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
        }
        .quick-link i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .quick-link span {
            font-size: 0.85rem;
            font-weight: 500;
        }
        .quick-link-form {
            display: contents;
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(16,185,129,0.15); color: #059669; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #d97706; }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #667eea;
            color: #667eea;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
    </style>
@endpush
