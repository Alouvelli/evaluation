@extends('layouts.admin')

@section('title', 'Liste des Enseignants')

@section('content')
    @php
        $total = $professeurs->count();
        // Calculer les stats si les moyennes sont disponibles
        $tresSatisfaisant = 0;
        $satisfaisant = 0;
        $peuSatisfaisant = 0;

        foreach($professeurs as $prof) {
            $moyenne = $prof->moyenne ?? null;
            if($moyenne !== null) {
                if($moyenne >= 85) $tresSatisfaisant++;
                elseif($moyenne >= 65) $satisfaisant++;
                else $peuSatisfaisant++;
            }
        }
    @endphp

        <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $total }}</h3>
                <p>Enseignants</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $tresSatisfaisant }}</h3>
                <p>Très satisfaisant</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $satisfaisant }}</h3>
                <p>Satisfaisant</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $peuSatisfaisant }}</h3>
                <p>Peu satisfaisant</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Liste des Enseignants</h3>
            <span class="badge-count">{{ $total }} enseignant(s)</span>
        </div>
        <div class="card-body">
            <!-- Vue Desktop -->
            <div class="table-scroll-wrapper">
                <table class="table-results datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th class="col-note">Note Finale</th>
                        <th>Appréciation</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($professeurs as $index => $prof)
                        @php
                            $moyenne = $prof->moyenne ?? 0;
                            $appreciation = $moyenne >= 85 ? 'Très satisfaisant' : ($moyenne >= 65 ? 'Satisfaisant' : 'Peu satisfaisant');
                            $appreciationClass = $moyenne >= 85 ? 'success' : ($moyenne >= 65 ? 'warning' : 'danger');
                            $noteClass = $moyenne >= 85 ? 'cell-good' : ($moyenne >= 65 ? 'cell-warning' : 'cell-danger');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="prof-info">
                                    <div class="prof-avatar-sm">
                                        {{ strtoupper(substr($prof->full_name ?? 'P', 0, 1)) }}
                                    </div>
                                    <strong>{{ $prof->full_name ?? 'N/A' }}</strong>
                                </div>
                            </td>
                            <td class="col-note {{ $noteClass }}">
                                <span class="note-value">{{ round($moyenne) }}/100</span>
                            </td>
                            <td>
                                    <span class="appreciation-badge badge-{{ $appreciationClass }}">
                                        @if($appreciationClass == 'success')
                                            <i class="fas fa-star"></i>
                                        @elseif($appreciationClass == 'warning')
                                            <i class="fas fa-check"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @endif
                                        {{ $appreciation }}
                                    </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('resultat_prof', $prof->id) }}" class="btn-action btn-view" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('rapport_prof', $prof->id) }}" class="btn-action btn-pdf" title="Rapport PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Vue Mobile -->
            <div class="mobile-cards">
                @foreach($professeurs as $index => $prof)
                    @php
                        $moyenne = $prof->moyenne ?? 0;
                        $appreciation = $moyenne >= 85 ? 'Très satisfaisant' : ($moyenne >= 65 ? 'Satisfaisant' : 'Peu satisfaisant');
                        $appreciationClass = $moyenne >= 85 ? 'success' : ($moyenne >= 65 ? 'warning' : 'danger');
                    @endphp
                    <div class="prof-card">
                        <div class="prof-card-header">
                            <div class="prof-info">
                                <div class="prof-avatar">
                                    {{ strtoupper(substr($prof->full_name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <h4>{{ $prof->full_name ?? 'N/A' }}</h4>
                                    <span class="appreciation-badge badge-{{ $appreciationClass }}">
                                        {{ $appreciation }}
                                    </span>
                                </div>
                            </div>
                            <div class="moyenne-badge moy-{{ $appreciationClass }}">
                                {{ round($moyenne) }}%
                            </div>
                        </div>
                        <div class="prof-card-footer">
                            <a href="{{ route('resultat_prof', $prof->id) }}" class="btn-card">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                            <a href="{{ route('rapport_prof', $prof->id) }}" class="btn-card btn-card-primary" target="_blank">
                                <i class="fas fa-file-pdf"></i> Rapport
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
