@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $professeurs->count() }}</h3>
                <p>Professeurs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cours->count() }}</h3>
                <p>Cours</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $classes->count() }}</h3>
                <p>Classes</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $questions->count() }}</h3>
                <p>Questions</p>
            </div>
        </div>
    </div>

    <!-- Main Card with Tabs -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-cogs"></i> Gestion des données</h3>
        </div>
        <div class="card-body">
            <!-- Tabs Navigation -->
            <div class="tabs-nav" style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <button class="tab-btn active" data-tab="professeurs">
                    <i class="fas fa-chalkboard-teacher"></i> Professeurs
                </button>
                <button class="tab-btn" data-tab="cours">
                    <i class="fas fa-book"></i> Cours
                </button>
                <button class="tab-btn" data-tab="classes">
                    <i class="fas fa-users"></i> Classes
                </button>
                <button class="tab-btn" data-tab="niveaux">
                    <i class="fas fa-layer-group"></i> Niveaux
                </button>
                <button class="tab-btn" data-tab="questions">
                    <i class="fas fa-question-circle"></i> Questions
                </button>
            </div>

            <!-- Tab: Professeurs -->
            <div class="tab-content active" id="tab-professeurs">
                <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="openModal('modalProfesseur')">
                        <i class="fas fa-plus"></i> Nouveau Professeur
                    </button>
                    @php
                        $profsAvecEmail = $professeurs->filter(fn($p) => !empty($p->email))->count();
                    @endphp
                    @if($profsAvecEmail > 0)
                        <form action="{{ route('send_all_rapports') }}" method="POST" style="display: inline;"
                              onsubmit="return confirm('Envoyer les rapports à tous les {{ $profsAvecEmail }} professeurs ayant un email ?')">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Envoyer tous les rapports ({{ $profsAvecEmail }})
                            </button>
                        </form>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th style="width: 280px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($professeurs as $prof)
                            <tr>
                                <td>{{ $prof->id }}</td>
                                <td>
                                    <strong>{{ $prof->full_name }}</strong>
                                </td>
                                <td>
                                    @if($prof->email)
                                        <a href="mailto:{{ $prof->email }}" class="email-link">
                                            <i class="fas fa-envelope"></i> {{ $prof->email }}
                                        </a>
                                    @else
                                        <span class="text-muted"><i class="fas fa-times-circle"></i> Non renseigné</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline" onclick="editProfesseur({{ $prof->id }}, '{{ addslashes($prof->full_name) }}', '{{ $prof->email }}')" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('deleteProfesseur', $prof->id) }}" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Supprimer ce professeur ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="{{ route('rapport', $prof->id) }}" class="btn btn-sm btn-secondary" target="_blank" title="Télécharger PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @if($prof->email)
                                            <a href="{{ route('send_rapport_prof', $prof->id) }}"
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Envoyer le rapport à {{ $prof->email }} ?')"
                                               title="Envoyer par email">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline" disabled title="Pas d'email">
                                                <i class="fas fa-paper-plane"></i>
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

            <!-- Tab: Cours -->
            <div class="tab-content" id="tab-cours">
                <div style="margin-bottom: 1rem;">
                    <button class="btn btn-primary" onclick="openModal('modalCours')">
                        <i class="fas fa-plus"></i> Nouveau Cours
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cours</th>
                            <th>Professeur</th>
                            <th>Classe</th>
                            <th>Semestre</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cours as $c)
                            <tr>
                                <td>{{ $c->id_cours }}</td>
                                <td><strong>{{ $c->libelle_cours }}</strong></td>
                                <td>{{ $c->professeur->full_name ?? 'N/A' }}</td>
                                <td>{{ $c->classe->libelle ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">S{{ $c->semestre }}</span>
                                </td>
                                <td>
                                    @if($c->etat == 1)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Actif</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fas fa-pause"></i> Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('modifyCours', $c->id_cours) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('deleteCours', $c->id_cours) }}" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Supprimer ce cours ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Classes -->
            <div class="tab-content" id="tab-classes">
                <div style="margin-bottom: 1rem;">
                    <button class="btn btn-primary" onclick="openModal('modalClasse')">
                        <i class="fas fa-plus"></i> Nouvelle Classe
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Libellé</th>
                            <th>Niveau</th>
                            <th>Nb Étudiants</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($classes as $classe)
                            <tr>
                                <td>{{ $classe->id }}</td>
                                <td><strong>{{ $classe->libelle }}</strong></td>
                                <td>
                                    <span class="badge badge-primary">{{ $classe->niveau->libelle_niveau ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $nbEtudiants = \App\Models\Etudiant::where('id_classe', $classe->id)->count();
                                    @endphp
                                    <span class="badge {{ $nbEtudiants > 0 ? 'badge-success' : 'badge-warning' }}">
                                        {{ $nbEtudiants }} étudiant(s)
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="editClasse({{ $classe->id }}, '{{ $classe->libelle }}', {{ $classe->id_niveau }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('deleteClasse', $classe->id) }}" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Supprimer cette classe ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Niveaux -->
            <div class="tab-content" id="tab-niveaux">
                <div style="margin-bottom: 1rem;">
                    <button class="btn btn-primary" onclick="openModal('modalNiveau')">
                        <i class="fas fa-plus"></i> Nouveau Niveau
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Libellé</th>
                            <th>Nb Classes</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($niveaux as $niveau)
                            <tr>
                                <td>{{ $niveau->id_niveau }}</td>
                                <td><strong>{{ $niveau->libelle_niveau }}</strong></td>
                                <td>
                                    @php
                                        $nbClasses = \App\Models\Classes::where('id_niveau', $niveau->id_niveau)->count();
                                    @endphp
                                    <span class="badge badge-primary">{{ $nbClasses }} classe(s)</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="editNiveau({{ $niveau->id_niveau }}, '{{ $niveau->libelle_niveau }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('deleteNiveau', $niveau->id_niveau) }}" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Supprimer ce niveau ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Questions -->
            <div class="tab-content" id="tab-questions">
                <div style="margin-bottom: 1rem;">
                    <button class="btn btn-primary" onclick="openModal('modalQuestion')">
                        <i class="fas fa-plus"></i> Nouvelle Question
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>Question</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($questions as $q)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">Q{{ $q->idQ }}</span>
                                </td>
                                <td>{{ $q->libelle }}</td>
                                <td>
                                    <a href="{{ route('deleteQuestion', $q->idQ) }}" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Supprimer cette question ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Nouveau Professeur -->
    <div class="modal-overlay" id="modalProfesseur">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-chalkboard-teacher"></i> Nouveau Professeur</h5>
                <button class="modal-close" onclick="closeModal('modalProfesseur')">&times;</button>
            </div>
            <form action="{{ route('newProfesseur') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="professeur" class="form-control" placeholder="Ex: Dr. Amadou DIALLO" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Ex: amadou.diallo@isi.edu.sn">
                        <small class="text-muted">L'email permet d'envoyer le rapport directement au professeur</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalProfesseur')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Professeur -->
    <div class="modal-overlay" id="modalEditProfesseur">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-edit"></i> Modifier Professeur</h5>
                <button class="modal-close" onclick="closeModal('modalEditProfesseur')">&times;</button>
            </div>
            <form action="{{ route('modifyProfesseur') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="editProfId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="professeur" id="editProfName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editProfEmail" class="form-control" placeholder="Ex: professeur@isi.edu.sn">
                        <small class="text-muted">L'email permet d'envoyer le rapport directement au professeur</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditProfesseur')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nouveau Cours -->
    <div class="modal-overlay" id="modalCours">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-book"></i> Nouveau Cours</h5>
                <button class="modal-close" onclick="closeModal('modalCours')">&times;</button>
            </div>
            <form action="{{ route('newCours') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé du cours <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="libelle_cours" class="form-control" placeholder="Ex: Algorithmique" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Professeur <span style="color: var(--danger);">*</span></label>
                        <select name="id_professeur" class="form-control form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($professeurs as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Classe <span style="color: var(--danger);">*</span></label>
                        <select name="id_classe" class="form-control form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->niveau->libelle_niveau ?? '' }} - {{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Année académique <span style="color: var(--danger);">*</span></label>
                        <select name="annee_id" class="form-control form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($annees as $an)
                                <option value="{{ $an->id }}">{{ $an->annee1 }} - {{ $an->annee2 }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semestre <span style="color: var(--danger);">*</span></label>
                        <select name="semestre" class="form-control form-select" required>
                            <option value="1">Semestre 1</option>
                            <option value="2">Semestre 2</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCours')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nouvelle Classe -->
    <div class="modal-overlay" id="modalClasse">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-users"></i> Nouvelle Classe</h5>
                <button class="modal-close" onclick="closeModal('modalClasse')">&times;</button>
            </div>
            <form action="{{ route('newClasse') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="classe" class="form-control" placeholder="Ex: GL-A" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau <span style="color: var(--danger);">*</span></label>
                        <select name="niveau_id" class="form-control form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id_niveau }}">{{ $niveau->libelle_niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalClasse')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Classe -->
    <div class="modal-overlay" id="modalEditClasse">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-edit"></i> Modifier Classe</h5>
                <button class="modal-close" onclick="closeModal('modalEditClasse')">&times;</button>
            </div>
            <form action="{{ route('updateClasse') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="editClasseId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé</label>
                        <input type="text" name="classe" id="editClasseLibelle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau</label>
                        <select name="niveau_id" id="editClasseNiveau" class="form-control form-select" required>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id_niveau }}">{{ $niveau->libelle_niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditClasse')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nouveau Niveau -->
    <div class="modal-overlay" id="modalNiveau">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-layer-group"></i> Nouveau Niveau</h5>
                <button class="modal-close" onclick="closeModal('modalNiveau')">&times;</button>
            </div>
            <form action="{{ route('newNiveau') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="niveau" class="form-control" placeholder="Ex: Licence 1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalNiveau')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Niveau -->
    <div class="modal-overlay" id="modalEditNiveau">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-edit"></i> Modifier Niveau</h5>
                <button class="modal-close" onclick="closeModal('modalEditNiveau')">&times;</button>
            </div>
            <form action="{{ route('updateNiveau') }}" method="POST">
                @csrf
                <input type="hidden" name="id_niveau" id="editNiveauId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé</label>
                        <input type="text" name="niveau" id="editNiveauLibelle" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditNiveau')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nouvelle Question -->
    <div class="modal-overlay" id="modalQuestion">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-question-circle"></i> Nouvelle Question</h5>
                <button class="modal-close" onclick="closeModal('modalQuestion')">&times;</button>
            </div>
            <form action="{{ route('newQuestion') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Question <span style="color: var(--danger);">*</span></label>
                        <textarea name="question" class="form-control" rows="3" placeholder="Ex: L'enseignant maîtrise-t-il sa matière ?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalQuestion')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .tabs-nav {
            border-bottom: 2px solid var(--gray-light);
            padding-bottom: 0.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.25rem;
            background: transparent;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 500;
            color: var(--gray);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-btn:hover {
            background: var(--light);
            color: var(--primary);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        .btn-group {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
        }

        .email-link {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
        }

        .email-link:hover {
            text-decoration: underline;
        }

        .text-muted {
            color: var(--gray);
            font-size: 0.8rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }

        small.text-muted {
            display: block;
            margin-top: 0.25rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Tabs Management
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active from all
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // Add active to clicked
                btn.classList.add('active');
                document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
            });
        });

        // Modal Management
        function openModal(id) {
            document.getElementById(id).classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });

        // Edit Functions
        function editProfesseur(id, name, email) {
            document.getElementById('editProfId').value = id;
            document.getElementById('editProfName').value = name;
            document.getElementById('editProfEmail').value = email || '';
            openModal('modalEditProfesseur');
        }

        function editClasse(id, libelle, niveauId) {
            document.getElementById('editClasseId').value = id;
            document.getElementById('editClasseLibelle').value = libelle;
            document.getElementById('editClasseNiveau').value = niveauId;
            openModal('modalEditClasse');
        }

        function editNiveau(id, libelle) {
            document.getElementById('editNiveauId').value = id;
            document.getElementById('editNiveauLibelle').value = libelle;
            openModal('modalEditNiveau');
        }
    </script>
@endpush
