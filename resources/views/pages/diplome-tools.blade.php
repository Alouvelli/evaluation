@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-row">
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
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="professeurs">
                    <i class="fas fa-chalkboard-teacher"></i> <span class="tab-text">Professeurs</span>
                </button>
                <button class="tab-btn" data-tab="cours">
                    <i class="fas fa-book"></i> <span class="tab-text">Cours</span>
                </button>
                <button class="tab-btn" data-tab="classes">
                    <i class="fas fa-users"></i> <span class="tab-text">Classes</span>
                </button>
                <button class="tab-btn" data-tab="niveaux">
                    <i class="fas fa-layer-group"></i> <span class="tab-text">Niveaux</span>
                </button>
                <button class="tab-btn" data-tab="questions">
                    <i class="fas fa-question-circle"></i> <span class="tab-text">Questions</span>
                </button>
            </div>

            <!-- Tab: Professeurs -->
            <div class="tab-content active" id="tab-professeurs">
                <div class="tab-actions">
                    <button class="btn btn-primary" onclick="openModal('modalProfesseur')">
                        <i class="fas fa-plus"></i> Nouveau Professeur
                    </button>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="table-results datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($professeurs as $prof)
                            <tr>
                                <td>{{ $prof->id }}</td>
                                <td><strong>{{ $prof->full_name }}</strong></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editProfesseur({{ $prof->id }}, '{{ $prof->full_name }}')" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('deleteProfesseur', $prof->id) }}" class="btn-action btn-danger" title="Supprimer"
                                           onclick="return confirm('Supprimer ce professeur ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="{{ route('rapport', $prof->id) }}" class="btn-action btn-pdf" target="_blank" title="Rapport PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
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
                <div class="tab-actions">
                    <button class="btn btn-primary" onclick="openModal('modalCours')">
                        <i class="fas fa-plus"></i> Nouveau Cours
                    </button>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="table-results datatable">
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
                                <td><span class="badge-info">S{{ $c->semestre }}</span></td>
                                <td>
                                    @if($c->etat == 1)
                                        <span class="status-badge status-active"><i class="fas fa-check"></i> Actif</span>
                                    @else
                                        <span class="status-badge status-pending"><i class="fas fa-pause"></i> Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('modifyCours', $c->id_cours) }}" class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('deleteCours', $c->id_cours) }}" class="btn-action btn-danger" title="Supprimer"
                                           onclick="return confirm('Supprimer ce cours ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Classes -->
            <div class="tab-content" id="tab-classes">
                <div class="tab-actions">
                    <button class="btn btn-primary" onclick="openModal('modalClasse')">
                        <i class="fas fa-plus"></i> Nouvelle Classe
                    </button>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="table-results datatable">
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
                                <td><span class="badge-primary">{{ $classe->niveau->libelle_niveau ?? 'N/A' }}</span></td>
                                <td>
                                    @php
                                        $nbEtudiants = \App\Models\Etudiant::where('id_classe', $classe->id)->count();
                                    @endphp
                                    <span class="badge-count-inline {{ $nbEtudiants > 0 ? 'success' : 'warning' }}">
                                        {{ $nbEtudiants }} étudiant(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editClasse({{ $classe->id }}, '{{ $classe->libelle }}', {{ $classe->id_niveau }})" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('deleteClasse', $classe->id) }}" class="btn-action btn-danger" title="Supprimer"
                                           onclick="return confirm('Supprimer cette classe ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        @if($nbEtudiants > 0)
                                            <a href="{{ route('etudiants.byClasse', $classe->id) }}" class="btn-action btn-view" title="Voir étudiants">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Niveaux -->
            <div class="tab-content" id="tab-niveaux">
                <div class="tab-actions">
                    <button class="btn btn-primary" onclick="openModal('modalNiveau')">
                        <i class="fas fa-plus"></i> Nouveau Niveau
                    </button>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="table-results datatable">
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
                                    <span class="badge-info">{{ $nbClasses }} classe(s)</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editNiveau({{ $niveau->id_niveau }}, '{{ $niveau->libelle_niveau }}')" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('deleteNiveau', $niveau->id_niveau) }}" class="btn-action btn-danger" title="Supprimer"
                                           onclick="return confirm('Supprimer ce niveau ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Questions -->
            <div class="tab-content" id="tab-questions">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>Les questions sont utilisées dans le formulaire d'évaluation. Modifier avec précaution.</div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Question</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($questions as $q)
                            <tr>
                                <td><span class="badge-primary">Q{{ $q->idQ }}</span></td>
                                <td>
                                    <form action="{{ route('modifyQuestion') }}" method="POST" class="question-form">
                                        @csrf
                                        <input type="hidden" name="idQ" value="{{ $q->idQ }}">
                                        <textarea name="libelle" rows="2" class="form-textarea">{{ $q->libelle }}</textarea>
                                        <button type="submit" class="btn-action btn-save" title="Enregistrer">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('deleteQuestion', $q->idQ) }}" class="btn-action btn-danger disabled" title="Supprimer"
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
                        <label class="form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" name="professeur" class="form-input" placeholder="Ex: Dr. Amadou DIALLO" required>
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
                        <label class="form-label">Nom complet</label>
                        <input type="text" name="professeur" id="editProfName" class="form-input" required>
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
                        <label class="form-label">Libellé du cours <span class="required">*</span></label>
                        <input type="text" name="libelle_cours" class="form-input" placeholder="Ex: Algorithmique" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Professeur <span class="required">*</span></label>
                        <select name="id_professeur" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($professeurs as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Classe <span class="required">*</span></label>
                        <select name="id_classe" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->niveau->libelle_niveau ?? '' }} - {{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Année académique <span class="required">*</span></label>
                        <select name="annee_id" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($annees as $an)
                                <option value="{{ $an->id }}">{{ $an->annee1 }} - {{ $an->annee2 }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semestre <span class="required">*</span></label>
                        <select name="semestre" class="form-select" required>
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
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="classe" class="form-input" placeholder="Ex: GL-A" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau <span class="required">*</span></label>
                        <select name="niveau_id" class="form-select" required>
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
                        <input type="text" name="classe" id="editClasseLibelle" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau</label>
                        <select name="niveau_id" id="editClasseNiveau" class="form-select" required>
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
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="niveau" class="form-input" placeholder="Ex: Licence 1" required>
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
                        <input type="text" name="niveau" id="editNiveauLibelle" class="form-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditNiveau')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tabs Management
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
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

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });

        // Edit Functions
        function editProfesseur(id, name) {
            document.getElementById('editProfId').value = id;
            document.getElementById('editProfName').value = name;
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
