@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users-cog"></i> Liste des utilisateurs</h3>
            <span class="badge-count">{{ $users->count() }} utilisateur(s)</span>
        </div>
        <div class="card-body">
            <div class="table-scroll-wrapper">
                <table class="table-results">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Campus</th>
                        <th>Rôle</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        @php
                            $currentUser = Auth::user();
                            $canManage = $currentUser->id != $user->id;
                            $isSuperAdmin = $currentUser->role === 'super_admin';
                            $sameCampus = $currentUser->campus_id == $user->campus_id || $isSuperAdmin;
                        @endphp

                        @if($sameCampus)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->id == $currentUser->id)
                                                <span class="badge-info" style="margin-left: 0.5rem;">Vous</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="email-link">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-primary">
                                        {{ $user->campus->nomCampus ?? 'Non défini' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->role === 'super_admin')
                                        <span class="role-badge role-super-admin">
                                            <i class="fas fa-crown"></i> Super Admin
                                        </span>
                                    @elseif($user->role === 'admin')
                                        <span class="role-badge role-admin">
                                            <i class="fas fa-user-shield"></i> Admin
                                        </span>
                                    @else
                                        <span class="role-badge role-user">
                                            <i class="fas fa-user"></i> Utilisateur
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->etat == 1)
                                        <span class="status-badge status-active">
                                            <i class="fas fa-check-circle"></i> Activé
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-times-circle"></i> Désactivé
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($canManage && $user->etat == 0)
                                            <a href="{{ route('activerUser', $user->id) }}"
                                               class="btn-action btn-success"
                                               title="Activer"
                                               onclick="return confirm('Activer cet utilisateur ?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif

                                        @if($canManage && $user->etat == 1)
                                            <a href="{{ route('admin.desactiver', $user->id) }}"
                                               class="btn-action btn-warning"
                                               title="Désactiver"
                                               onclick="return confirm('Désactiver cet utilisateur ?')">
                                                <i class="fas fa-pause"></i>
                                            </a>
                                        @endif

                                        @if($canManage && $user->role !== 'admin' && $user->role !== 'super_admin')
                                            <a href="{{ route('defineAdmin', $user->id) }}"
                                               class="btn-action btn-secondary"
                                               title="Définir admin"
                                               onclick="return confirm('Définir comme admin ?')">
                                                <i class="fas fa-user-shield"></i>
                                            </a>
                                        @endif

                                        @if($canManage)
                                            <a href="{{ route('deleteUser', $user->id) }}"
                                               class="btn-action btn-danger"
                                               title="Supprimer"
                                               onclick="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endif

                                        @if(!$canManage)
                                            <span class="text-muted">--</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend Card -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Légende des rôles</h3>
        </div>
        <div class="card-body">
            <div class="roles-grid">
                <div class="role-info-card">
                    <span class="role-badge role-super-admin">
                        <i class="fas fa-crown"></i> Super Admin
                    </span>
                    <p>Accès total à tous les campus et fonctionnalités</p>
                </div>
                <div class="role-info-card">
                    <span class="role-badge role-admin">
                        <i class="fas fa-user-shield"></i> Admin
                    </span>
                    <p>Gestion complète de son campus</p>
                </div>
                <div class="role-info-card">
                    <span class="role-badge role-user">
                        <i class="fas fa-user"></i> Utilisateur
                    </span>
                    <p>Accès aux fonctionnalités de base</p>
                </div>
            </div>
        </div>
    </div>
@endsection
