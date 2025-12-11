@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.admin')

@section('title', 'Gestion des données')

@php
    // Récupérer l'année et semestre actifs automatiquement
    $coursActif = \App\Models\Cours::where('campus_id', Auth::user()->campus_id)
        ->where('etat', 1)
        ->first();
    $anneeActiveId = $coursActif->annee_id ?? ($annees->first()->id ?? null);
    $semestreActif = $coursActif->semestre ?? 1;

    // Professeurs avec email pour envoi rapports
    $profsAvecEmail = $professeurs->filter(fn($p) => !empty($p->email))->count();
@endphp

@section('content')
    <!-- Loader Overlay -->
    <div id="loader-overlay">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <p>Chargement en cours...</p>
        </div>
    </div>

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
            <div class="tabs-nav">
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

            <!-- ==================== Tab: Professeurs ==================== -->
            <div class="tab-content active" id="tab-professeurs">
                <div class="tab-toolbar">
                    <div class="toolbar-left">
                        <button class="btn btn-primary" onclick="openModal('modalProfesseur')">
                            <i class="fas fa-plus"></i> Nouveau
                        </button>
                        @if($profsAvecEmail > 0)
                            <form action="{{ route('send_all_rapports') }}" method="POST" style="display: inline;" onsubmit="return confirmSendAll()">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-envelope"></i> Envoyer rapports ({{ $profsAvecEmail }})
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="toolbar-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchProfesseurs" placeholder="Rechercher un professeur..." onkeyup="filterTable('tableProfesseurs', this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tableProfesseurs">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($professeurs as $index => $prof)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $prof->full_name }}</strong></td>
                                <td>
                                    @if($prof->email)
                                        <span class="email-badge"><i class="fas fa-envelope"></i> {{ $prof->email }}</span>
                                    @else
                                        <span class="text-muted">Non renseigné</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editProfesseur({{ $prof->id }}, '{{ addslashes($prof->full_name) }}', '{{ $prof->email }}')" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('rapport', $prof->id) }}" class="btn-action btn-pdf" target="_blank" title="Télécharger rapport">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @if($prof->email)
                                            <a href="{{ route('send_rapport_prof', $prof->id) }}" class="btn-action btn-email" title="Envoyer par email" onclick="showLoader()">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        @endif
                                        <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('deleteProfesseur', $prof->id) }}', 'ce professeur')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-footer" id="footerProfesseurs"></div>
            </div>

            <!-- ==================== Tab: Cours ==================== -->
            <div class="tab-content" id="tab-cours">
                <div class="tab-toolbar">
                    <div class="toolbar-left">
                        <button class="btn btn-primary" onclick="openModal('modalCours')">
                            <i class="fas fa-plus"></i> Nouveau
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchCours" placeholder="Rechercher un cours..." onkeyup="filterTable('tableCours', this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tableCours">
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
                                <td><span class="badge badge-info">S{{ $c->semestre }}</span></td>
                                <td>
                                    @if($c->etat == 1)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Actif</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-pause"></i> Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('modifyCours', $c->id_cours) }}" class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('deleteCours', $c->id_cours) }}', 'ce cours')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-footer" id="footerCours"></div>
            </div>

            <!-- ==================== Tab: Classes ==================== -->
            <div class="tab-content" id="tab-classes">
                <div class="tab-toolbar">
                    <div class="toolbar-left">
                        <button class="btn btn-primary" onclick="openModal('modalClasse')">
                            <i class="fas fa-plus"></i> Nouvelle
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchClasses" placeholder="Rechercher une classe..." onkeyup="filterTable('tableClasses', this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tableClasses">
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
                                <td><span class="badge badge-primary">{{ $classe->niveau->libelle_niveau ?? 'N/A' }}</span></td>
                                <td>
                                    @php $nbEtudiants = \App\Models\Etudiant::where('id_classe', $classe->id)->count(); @endphp
                                    <span class="badge {{ $nbEtudiants > 0 ? 'badge-success' : 'badge-warning' }}">
                                        {{ $nbEtudiants }} étudiant(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editClasse({{ $classe->id }}, '{{ $classe->libelle }}', {{ $classe->id_niveau }})" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('deleteClasse', $classe->id) }}', 'cette classe')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-footer" id="footerClasses"></div>
            </div>

            <!-- ==================== Tab: Niveaux ==================== -->
            <div class="tab-content" id="tab-niveaux">
                <div class="tab-toolbar">
                    <div class="toolbar-left">
                        <button class="btn btn-primary" onclick="openModal('modalNiveau')">
                            <i class="fas fa-plus"></i> Nouveau
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchNiveaux" placeholder="Rechercher un niveau..." onkeyup="filterTable('tableNiveaux', this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tableNiveaux">
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
                                    @php $nbClasses = \App\Models\Classes::where('id_niveau', $niveau->id_niveau)->count(); @endphp
                                    <span class="badge badge-primary">{{ $nbClasses }} classe(s)</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editNiveau({{ $niveau->id_niveau }}, '{{ $niveau->libelle_niveau }}')" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('deleteNiveau', $niveau->id_niveau) }}', 'ce niveau')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-footer" id="footerNiveaux"></div>
            </div>

            <!-- ==================== Tab: Questions ==================== -->
            <div class="tab-content" id="tab-questions">
                <div class="tab-toolbar">
                    <div class="toolbar-left">
                        <button class="btn btn-primary" onclick="openModal('modalQuestion')">
                            <i class="fas fa-plus"></i> Nouvelle
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchQuestions" placeholder="Rechercher une question..." onkeyup="filterTable('tableQuestions', this.value)">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tableQuestions">
                        <thead>
                        <tr>
                            <th style="width: 60px;">N°</th>
                            <th>Question</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($questions as $q)
                            <tr>
                                <td><span class="badge badge-primary">Q{{ $q->idQ }}</span></td>
                                <td>{{ $q->libelle }}</td>
                                <td>
                                    <button class="btn-action btn-delete" onclick="confirmDelete('{{ route('deleteQuestion', $q->idQ) }}', 'cette question')" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-footer" id="footerQuestions"></div>
            </div>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Modal: Nouveau Professeur -->
    <div class="modal-overlay" id="modalProfesseur">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-chalkboard-teacher"></i> Nouveau Professeur</h5>
                <button class="modal-close" onclick="closeModal('modalProfesseur')">&times;</button>
            </div>
            <form id="formProfesseur" action="{{ route('newProfesseur') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_tab" value="professeurs">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" name="professeur" class="form-control" placeholder="Ex: Pr. Alassane SECK" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control email-org" placeholder="Ex: aseck@groupeisi.com">
                        <small class="form-hint"><i class="fas fa-info-circle"></i> Seuls les emails @groupeisi.com sont autorisés</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalProfesseur')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
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
            <form id="formEditProfesseur" action="{{ route('modifyProfesseur') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="editProfId">
                <input type="hidden" name="redirect_tab" value="professeurs">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" name="professeur" id="editProfName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editProfEmail" class="form-control email-org">
                        <small class="form-hint"><i class="fas fa-info-circle"></i> Seuls les emails @groupeisi.com sont autorisés</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditProfesseur')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nouveau Cours -->
    <div class="modal-overlay" id="modalCours">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5><i class="fas fa-book"></i> Nouveau Cours</h5>
                <button class="modal-close" onclick="closeModal('modalCours')">&times;</button>
            </div>
            <form id="formCours" action="{{ route('newCours') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_tab" value="cours">
                <input type="hidden" name="annee_id" value="{{ $anneeActiveId }}">
                <input type="hidden" name="semestre" value="{{ $semestreActif }}">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Période active : <strong>{{ $annees->firstWhere('id', $anneeActiveId)->annee1 ?? '' }}-{{ $annees->firstWhere('id', $anneeActiveId)->annee2 ?? '' }}</strong>
                        / <strong>Semestre {{ $semestreActif }}</strong>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Libellé du cours <span class="required">*</span></label>
                        <input type="text" name="libelle_cours" class="form-control" placeholder="Ex: Algorithmique" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Professeur <span class="required">*</span></label>
                                <select name="id_professeur" class="form-control select2-search" required>
                                    <option value="">-- Rechercher un professeur --</option>
                                    @foreach($professeurs as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Classe <span class="required">*</span></label>
                                <select name="id_classe" class="form-control select2-search" required>
                                    <option value="">-- Rechercher une classe --</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $classe->niveau->libelle_niveau ?? '' }} - {{ $classe->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCours')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
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
            <form id="formClasse" action="{{ route('newClasse') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_tab" value="classes">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="classe" class="form-control" placeholder="Ex: GL-A" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau <span class="required">*</span></label>
                        <select name="niveau_id" class="form-control select2-search" required>
                            <option value="">-- Rechercher un niveau --</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id_niveau }}">{{ $niveau->libelle_niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalClasse')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
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
                <input type="hidden" name="redirect_tab" value="classes">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="classe" id="editClasseLibelle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Niveau <span class="required">*</span></label>
                        <select name="niveau_id" id="editClasseNiveau" class="form-control select2-search-edit" required>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id_niveau }}">{{ $niveau->libelle_niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditClasse')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modifier</button>
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
                <input type="hidden" name="redirect_tab" value="niveaux">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="niveau" class="form-control" placeholder="Ex: Licence 1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalNiveau')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
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
                <input type="hidden" name="redirect_tab" value="niveaux">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Libellé <span class="required">*</span></label>
                        <input type="text" name="niveau" id="editNiveauLibelle" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditNiveau')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modifier</button>
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
                <input type="hidden" name="redirect_tab" value="questions">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Question <span class="required">*</span></label>
                        <textarea name="question" class="form-control" rows="3" placeholder="Ex: L'enseignant maîtrise-t-il sa matière ?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalQuestion')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loader-content { text-align: center; }
        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        #loader-overlay p { color: #667eea; font-weight: 600; font-size: 1.1rem; }

        /* Alert */
        .alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-info { background: rgba(102, 126, 234, 0.1); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.3); }
        .alert i { margin-right: 0.5rem; }

        /* Tabs */
        .tabs-nav { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; }
        .tab-btn { padding: 0.75rem 1.25rem; background: transparent; border: none; border-radius: 8px 8px 0 0; font-family: inherit; font-weight: 500; color: #64748b; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem; }
        .tab-btn:hover { background: #f1f5f9; color: #667eea; }
        .tab-btn.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Toolbar */
        .tab-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .toolbar-left { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .toolbar-right { display: flex; gap: 0.5rem; align-items: center; }

        /* Search Box */
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
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Table */
        .table-responsive { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table th { background: #f8fafc; font-weight: 600; color: #374151; font-size: 0.85rem; text-transform: uppercase; }
        .table tbody tr:hover { background: #f8fafc; }
        .table tbody tr.hidden { display: none; }

        /* Table Footer */
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-top: 1px solid #e2e8f0;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }
        .pagination-btns { display: flex; gap: 0.25rem; }
        .pagination-btns button {
            padding: 0.4rem 0.75rem;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .pagination-btns button:hover:not(:disabled) { background: #667eea; color: white; border-color: #667eea; }
        .pagination-btns button:disabled { opacity: 0.5; cursor: not-allowed; }
        .pagination-btns button.active { background: #667eea; color: white; border-color: #667eea; }

        /* Action buttons */
        .action-buttons { display: flex; gap: 0.35rem; }
        .btn-action { width: 32px; height: 32px; border: none; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; color: white; text-decoration: none; font-size: 0.85rem; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: white; }
        .btn-edit { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .btn-delete { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .btn-pdf { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .btn-email { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

        /* Badges */
        .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-primary { background: rgba(102, 126, 234, 0.15); color: #667eea; }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: #065f46; }
        .badge-warning { background: rgba(245, 158, 11, 0.15); color: #92400e; }
        .badge-info { background: rgba(59, 130, 246, 0.15); color: #1d4ed8; }
        .badge-secondary { background: rgba(100, 116, 139, 0.15); color: #475569; }
        .email-badge { color: #667eea; font-size: 0.85rem; }
        .text-muted { color: #94a3b8; font-size: 0.85rem; font-style: italic; }

        /* Form */
        .required { color: #ef4444; }
        .form-hint { display: block; margin-top: 0.35rem; font-size: 0.75rem; color: #64748b; }
        .form-hint i { margin-right: 0.25rem; }
        .row { display: flex; gap: 1rem; }
        .col-6 { flex: 1; }
        .modal-lg { max-width: 600px; }

        /* Select2 Custom Style */
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: #374151;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: #667eea;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .select2-search--dropdown .select2-search__field {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            outline: none;
            border-color: #667eea;
        }

        /* Stats grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .row { flex-direction: column; gap: 0; }
            .tabs-nav { gap: 0.25rem; }
            .tab-btn { padding: 0.5rem 0.75rem; font-size: 0.85rem; }
            .tab-toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left, .toolbar-right { width: 100%; }
            .search-box { min-width: 100%; }
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==================== LOADER ====================
        function showLoader() { document.getElementById('loader-overlay').style.display = 'flex'; }
        function hideLoader() { document.getElementById('loader-overlay').style.display = 'none'; }

        // ==================== TABS ====================
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
                history.replaceState(null, null, '#' + btn.dataset.tab);
                // Init pagination for this tab
                initPagination('table' + capitalizeFirst(btn.dataset.tab), 'footer' + capitalizeFirst(btn.dataset.tab));
            });
        });

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Restore tab from URL hash or session
        document.addEventListener('DOMContentLoaded', function() {
            hideLoader();
            const hash = window.location.hash.replace('#', '');
            const savedTab = '{{ session('redirect_tab', '') }}';
            const tabToOpen = hash || savedTab || 'professeurs';
            const tabBtn = document.querySelector(`.tab-btn[data-tab="${tabToOpen}"]`);
            if (tabBtn) tabBtn.click();

            // Init Select2
            initSelect2();
        });

        // ==================== SELECT2 (Recherche dans les listes déroulantes) ====================
        function initSelect2() {
            $('.select2-search').select2({
                placeholder: 'Tapez pour rechercher...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() { return "Aucun résultat trouvé"; },
                    searching: function() { return "Recherche en cours..."; },
                    inputTooShort: function() { return "Tapez pour rechercher..."; }
                },
                dropdownParent: $('.modal-overlay.show').length ? $('.modal-overlay.show') : $('body')
            });
        }

        // Re-init Select2 when modal opens
        function openModal(id) {
            document.getElementById(id).classList.add('show');
            setTimeout(() => {
                $('#' + id + ' .select2-search').select2({
                    placeholder: 'Tapez pour rechercher...',
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() { return "Aucun résultat trouvé"; },
                        searching: function() { return "Recherche en cours..."; }
                    },
                    dropdownParent: $('#' + id)
                });
            }, 100);
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
            $('#' + id + ' .select2-search').select2('destroy');
        }

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    $(modal).find('.select2-search').select2('destroy');
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(m => {
                    m.classList.remove('show');
                    $(m).find('.select2-search').select2('destroy');
                });
            }
        });

        // ==================== TABLE SEARCH ====================
        function filterTable(tableId, query) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const rows = table.querySelectorAll('tbody tr');
            const q = query.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (q === '' || text.includes(q)) {
                    row.classList.remove('hidden');
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                    row.style.display = 'none';
                }
            });

            // Update pagination after filter
            const footerId = tableId.replace('table', 'footer');
            initPagination(tableId, footerId);
        }

        // ==================== PAGINATION ====================
        const ITEMS_PER_PAGE = 10;
        let paginationStates = {};

        function initPagination(tableId, footerId) {
            const table = document.getElementById(tableId);
            const footer = document.getElementById(footerId);
            if (!table || !footer) return;

            const tbody = table.querySelector('tbody');
            const allRows = Array.from(tbody.querySelectorAll('tr:not(.hidden)'));

            if (allRows.length === 0) {
                footer.innerHTML = '<span>Aucun élément trouvé</span>';
                return;
            }

            if (allRows.length <= ITEMS_PER_PAGE) {
                footer.innerHTML = `<span>Affichage de ${allRows.length} élément(s)</span><div></div>`;
                allRows.forEach(row => row.style.display = '');
                return;
            }

            paginationStates[tableId] = {
                currentPage: 1,
                totalPages: Math.ceil(allRows.length / ITEMS_PER_PAGE),
                rows: allRows
            };

            showPage(tableId, footerId, 1);
        }

        function showPage(tableId, footerId, page) {
            const state = paginationStates[tableId];
            if (!state) return;

            state.currentPage = page;
            const start = (page - 1) * ITEMS_PER_PAGE;
            const end = start + ITEMS_PER_PAGE;

            state.rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            renderPaginationUI(tableId, footerId);
        }

        function renderPaginationUI(tableId, footerId) {
            const footer = document.getElementById(footerId);
            const state = paginationStates[tableId];
            if (!footer || !state) return;

            const start = (state.currentPage - 1) * ITEMS_PER_PAGE + 1;
            const end = Math.min(state.currentPage * ITEMS_PER_PAGE, state.rows.length);

            let pagesHtml = '';
            for (let i = Math.max(1, state.currentPage - 2); i <= Math.min(state.totalPages, state.currentPage + 2); i++) {
                pagesHtml += `<button class="${i === state.currentPage ? 'active' : ''}" onclick="showPage('${tableId}', '${footerId}', ${i})">${i}</button>`;
            }

            footer.innerHTML = `
            <span>Affichage ${start}-${end} sur ${state.rows.length} éléments</span>
            <div class="pagination-btns">
                <button onclick="showPage('${tableId}', '${footerId}', 1)" ${state.currentPage === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>
                <button onclick="showPage('${tableId}', '${footerId}', ${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>
                ${pagesHtml}
                <button onclick="showPage('${tableId}', '${footerId}', ${state.currentPage + 1})" ${state.currentPage === state.totalPages ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>
                <button onclick="showPage('${tableId}', '${footerId}', ${state.totalPages})" ${state.currentPage === state.totalPages ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>
            </div>
        `;
        }

        // ==================== EDIT FUNCTIONS ====================
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

        // ==================== DELETE CONFIRMATION ====================
        function confirmDelete(url, item) {
            Swal.fire({
                title: 'Confirmation',
                text: `Voulez-vous vraiment supprimer ${item} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash"></i> Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    window.location.href = url;
                }
            });
        }

        function confirmSendAll() {
            Swal.fire({
                title: 'Envoi en masse',
                text: 'Voulez-vous envoyer les rapports à tous les professeurs ayant un email ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Oui, envoyer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    return true;
                }
                return false;
            });
            return false; // Prevent default, handled by Swal
        }

        // ==================== EMAIL VALIDATION ====================
        document.querySelectorAll('.email-org').forEach(input => {
            input.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !email.toLowerCase().endsWith('@groupeisi.com')) {
                    Swal.fire({
                        title: 'Email non autorisé',
                        html: 'Seuls les emails <strong>@groupeisi.com</strong> sont acceptés.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    this.value = '';
                    this.focus();
                }
            });
        });

        // ==================== FORM SUBMIT ====================
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const emailInput = this.querySelector('.email-org');
                if (emailInput && emailInput.value.trim()) {
                    if (!emailInput.value.toLowerCase().endsWith('@groupeisi.com')) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Email non autorisé',
                            html: 'Seuls les emails <strong>@groupeisi.com</strong> sont acceptés.',
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                }
                showLoader();
            });
        });

        // ==================== SWEET ALERTS FOR STATUS ====================
        @if(session('status'))
        @php
            $status = session('status');
            $isError = str_contains($status, 'Erreur') || str_contains($status, 'Impossible') || str_contains($status, 'existe déjà') || str_contains($status, 'invalide');
        @endphp
        Swal.fire({
            title: '{{ $isError ? "Erreur" : "Succès" }}',
            text: '{{ addslashes($status) }}',
            icon: '{{ $isError ? "error" : "success" }}',
            confirmButtonColor: '#667eea',
            timer: {{ $isError ? '5000' : '3000' }},
            timerProgressBar: true
        });
        @endif
    </script>
@endpush
