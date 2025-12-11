@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h1>
            <p class="subtitle">Gérer tous les utilisateurs de la plateforme</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button class="btn btn-primary" onclick="openModal('modalUser')">
                <i class="fas fa-plus"></i> Nouvel Utilisateur
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>{{ $users->count() }}</h3>
                <p>Total utilisateurs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-user-check"></i></div>
            <div class="stat-content">
                <h3>{{ $users->where('etat', 1)->count() }}</h3>
                <p>Actifs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-user-shield"></i></div>
            <div class="stat-content">
                <h3>{{ $users->where('role', 'admin')->count() }}</h3>
                <p>Administrateurs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="fas fa-crown"></i></div>
            <div class="stat-content">
                <h3>{{ $users->where('role', 'super_admin')->count() }}</h3>
                <p>Super Admins</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Liste des Utilisateurs</h3>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchUsers" placeholder="Rechercher..." onkeyup="filterTable('tableUsers', this.value)">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="tableUsers">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Campus</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge badge-danger"><i class="fas fa-crown"></i> Super Admin</span>
                                @elseif($user->role === 'admin')
                                    <span class="badge badge-warning"><i class="fas fa-user-shield"></i> Admin</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-user"></i> Utilisateur</span>
                                @endif
                            </td>
                            <td>
                                @if($user->campus)
                                    <span class="badge badge-primary">{{ $user->campus->nomCampus }}</span>
                                @else
                                    <span class="text-muted">Tous</span>
                                @endif
                            </td>
                            <td>
                                @if($user->etat == 1)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Actif</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-ban"></i> Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editUser({{ json_encode($user) }})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('super-admin.users.delete', $user->id) }}', 'cet utilisateur')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Nouvel Utilisateur -->
    <div class="modal-overlay" id="modalUser">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-user-plus"></i> Nouvel Utilisateur</h5>
                <button class="modal-close" onclick="closeModal('modalUser')">&times;</button>
            </div>
            <form action="{{ route('super-admin.users.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Rôle <span class="required">*</span></label>
                                <select name="role" id="newUserRole" class="form-control" required onchange="toggleCampusField('new')">
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group" id="newCampusGroup">
                                <label class="form-label">Campus <span class="required">*</span></label>
                                <select name="campus_id" id="newUserCampus" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->nomCampus }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalUser')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Modifier Utilisateur -->
    <div class="modal-overlay" id="modalEditUser">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-user-edit"></i> Modifier Utilisateur</h5>
                <button class="modal-close" onclick="closeModal('modalEditUser')">&times;</button>
            </div>
            <form id="formEditUser" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom <span class="required">*</span></label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe <small>(laisser vide pour ne pas changer)</small></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Rôle <span class="required">*</span></label>
                                <select name="role" id="editUserRole" class="form-control" required onchange="toggleCampusField('edit')">
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group" id="editCampusGroup">
                                <label class="form-label">Campus <span class="required">*</span></label>
                                <select name="campus_id" id="editUserCampus" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->nomCampus }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">État</label>
                        <select name="etat" id="editUserEtat" class="form-control">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditUser')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modifier</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        .page-header .subtitle { margin: 0.5rem 0 0; color: #64748b; }
        .header-actions { display: flex; gap: 0.5rem; }

        .stats-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 992px) { .stats-grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .stats-grid-4 { grid-template-columns: 1fr; } }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
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
        .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .search-box {
            position: relative;
            min-width: 250px;
        }
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-box input {
            width: 100%;
            padding: 0.6rem 0.75rem 0.6rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .search-box input:focus { outline: none; border-color: #667eea; }

        .badge-danger { background: rgba(239,68,68,0.15); color: #dc2626; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #d97706; }
        .badge-success { background: rgba(16,185,129,0.15); color: #059669; }
        .badge-primary { background: rgba(102,126,234,0.15); color: #667eea; }
        .badge-secondary { background: rgba(100,116,139,0.15); color: #64748b; }

        .row { display: flex; gap: 1rem; }
        .col-6 { flex: 1; }
        @media (max-width: 576px) { .row { flex-direction: column; } }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        function toggleCampusField(type) {
            const roleSelect = document.getElementById(type === 'new' ? 'newUserRole' : 'editUserRole');
            const campusGroup = document.getElementById(type === 'new' ? 'newCampusGroup' : 'editCampusGroup');
            const campusSelect = document.getElementById(type === 'new' ? 'newUserCampus' : 'editUserCampus');

            if (roleSelect.value === 'super_admin') {
                campusGroup.style.display = 'none';
                campusSelect.removeAttribute('required');
            } else {
                campusGroup.style.display = 'block';
                campusSelect.setAttribute('required', 'required');
            }
        }

        function editUser(user) {
            document.getElementById('formEditUser').action = '/super-admin/users/' + user.id + '/update';
            document.getElementById('editUserName').value = user.name;
            document.getElementById('editUserEmail').value = user.email;
            document.getElementById('editUserRole').value = user.role;
            document.getElementById('editUserCampus').value = user.campus_id || '';
            document.getElementById('editUserEtat').value = user.etat;
            toggleCampusField('edit');
            openModal('modalEditUser');
        }

        function filterTable(tableId, query) {
            const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
            const q = query.toLowerCase();
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        function confirmDelete(url, item) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous vraiment supprimer ' + item + ' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        @if(session('status'))
        Swal.fire({
            title: '{{ str_contains(session('status'), 'Erreur') ? 'Erreur' : 'Succès' }}',
            text: '{{ session('status') }}',
            icon: '{{ str_contains(session('status'), 'Erreur') ? 'error' : 'success' }}',
            timer: 3000,
            timerProgressBar: true
        });
        @endif
    </script>
@endpush
