@extends('layouts.admin')

@section('title', 'Gestion des Campus')

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-building"></i> Gestion des Campus</h1>
            <p class="subtitle">Gérer tous les campus de l'établissement</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button class="btn btn-primary" onclick="openModal('modalCampus')">
                <i class="fas fa-plus"></i> Nouveau Campus
            </button>
        </div>
    </div>

    <!-- Campus Cards -->
    <div class="campus-grid">
        @foreach($campuses as $campus)
            <div class="campus-card">
                <div class="campus-header">
                    <h3><i class="fas fa-building"></i> {{ $campus->nomCampus }}</h3>
                    <div class="campus-actions">
                        <button class="btn-icon btn-edit" onclick="editCampus({{ $campus->id }}, '{{ $campus->nomCampus }}')" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-delete" onclick="confirmDelete('{{ route('super-admin.campuses.delete', $campus->id) }}', 'ce campus')" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="campus-body">
                    <div class="campus-stat-grid">
                        <div class="mini-stat">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>{{ $campus->classes_count }}</strong>
                                <span>Classes</span>
                            </div>
                        </div>
                        <div class="mini-stat">
                            <i class="fas fa-user-graduate"></i>
                            <div>
                                <strong>{{ $campus->etudiants_count }}</strong>
                                <span>Étudiants</span>
                            </div>
                        </div>
                        <div class="mini-stat">
                            <i class="fas fa-book"></i>
                            <div>
                                <strong>{{ $campus->cours_count }}</strong>
                                <span>Cours</span>
                            </div>
                        </div>
                        <div class="mini-stat">
                            <i class="fas fa-user-shield"></i>
                            <div>
                                <strong>{{ $campus->users_count }}</strong>
                                <span>Utilisateurs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="campus-footer">
                    <form action="{{ route('super-admin.switch-campus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="campus_id" value="{{ $campus->id }}">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Accéder à ce campus
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal: Nouveau Campus -->
    <div class="modal-overlay" id="modalCampus">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-building"></i> Nouveau Campus</h5>
                <button class="modal-close" onclick="closeModal('modalCampus')">&times;</button>
            </div>
            <form action="{{ route('super-admin.campuses.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom du Campus <span class="required">*</span></label>
                        <input type="text" name="nomCampus" class="form-control" placeholder="Ex: Campus Dakar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCampus')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Modifier Campus -->
    <div class="modal-overlay" id="modalEditCampus">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-edit"></i> Modifier Campus</h5>
                <button class="modal-close" onclick="closeModal('modalEditCampus')">&times;</button>
            </div>
            <form id="formEditCampus" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom du Campus <span class="required">*</span></label>
                        <input type="text" name="nomCampus" id="editCampusNom" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditCampus')">Annuler</button>
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
        .header-actions { display: flex; gap: 0.5rem; }

        .campus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .campus-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .campus-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .campus-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .campus-header h3 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .campus-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .btn-icon:hover {
            background: rgba(255,255,255,0.3);
        }

        .campus-body {
            padding: 1.5rem;
        }
        .campus-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .mini-stat {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 10px;
        }
        .mini-stat i {
            font-size: 1.25rem;
            color: #667eea;
        }
        .mini-stat strong {
            display: block;
            font-size: 1.25rem;
            color: #1e293b;
        }
        .mini-stat span {
            font-size: 0.75rem;
            color: #64748b;
        }

        .campus-footer {
            padding: 1rem 1.5rem 1.5rem;
        }
        .btn-block {
            width: 100%;
            justify-content: center;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        function editCampus(id, nom) {
            document.getElementById('formEditCampus').action = '/super-admin/campuses/' + id + '/update';
            document.getElementById('editCampusNom').value = nom;
            openModal('modalEditCampus');
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
