@extends('layouts.admin')

@section('title', 'Import des Étudiants')

@section('content')
    <div class="grid-2-cols">
        <!-- Import Form Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-import"></i> Importer des Étudiants</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('import_etudiants') }}" enctype="multipart/form-data" class="form-modern">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users"></i> Classe <span class="required">*</span>
                        </label>
                        <select class="form-select" name="classe" required>
                            <option value="">-- Sélectionner une classe --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">
                                    {{ $classe->niveau->libelle_niveau ?? '' }} - {{ $classe->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-file-excel"></i> Fichier Excel <span class="required">*</span>
                        </label>
                        <div class="file-upload">
                            <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                            <label for="fileInput" class="file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="fileName">Cliquer pour choisir un fichier</span>
                            </label>
                        </div>
                        <small class="form-hint">Formats acceptés : .xlsx, .xls, .csv (max 5 Mo)</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-upload"></i> Importer les étudiants
                    </button>
                </form>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Instructions</h3>
            </div>
            <div class="card-body">
                <h4 class="section-title">Format du fichier Excel :</h4>
                <p class="section-text">
                    Le fichier doit contenir une colonne <strong>matricule</strong> avec les matricules des étudiants.
                </p>

                <div class="example-table">
                    <table>
                        <thead>
                        <tr>
                            <th>matricule</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td>L1DS& BD-25-4003</td></tr>
                        <tr><td>L3GL-25-3699</td></tr>
                        <tr><td>M1RESI-25-3678</td></tr>
                        <tr><td>...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <strong>Note :</strong> Les matricules peuvent être au format
                        <code>L1DS& BD-25-4003</code> ou <code>L1DS& BD254003</code>.
                        Les tirets seront automatiquement supprimés.
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Attention :</strong> L'import réinitialise le statut d'évaluation
                        des étudiants existants de la classe sélectionnée.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students by Class -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Étudiants par classe</h3>
        </div>
        <div class="card-body">
            <div class="table-scroll-wrapper">
                <table class="table-results">
                    <thead>
                    <tr>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Nb. Étudiants</th>
                        <th>Ont évalué</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($classes as $classe)
                        @php
                            $nbEtudiants = \App\Models\Etudiant::where('id_classe', $classe->id)->count();
                            $nbEvalues = \App\Models\Etudiant::where('id_classe', $classe->id)->where('statut', \App\Models\Etudiant::STATUT_A_EVALUE)->count();
                            $pourcentage = $nbEtudiants > 0 ? round(($nbEvalues / $nbEtudiants) * 100) : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $classe->libelle }}</strong></td>
                            <td>
                                <span class="badge-primary">{{ $classe->niveau->libelle_niveau ?? 'N/A' }}</span>
                            </td>
                            <td>
                                    <span class="badge-count-inline {{ $nbEtudiants > 0 ? 'success' : 'warning' }}">
                                        {{ $nbEtudiants }} étudiant(s)
                                    </span>
                            </td>
                            <td>
                                @if($nbEtudiants > 0)
                                    <div class="progress-wrapper">
                                        <div class="progress-bar-mini">
                                            <div class="progress-fill success" style="width: {{ $pourcentage }}%;"></div>
                                        </div>
                                        <span class="progress-value">{{ $nbEvalues }}/{{ $nbEtudiants }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td>
                                @if($nbEtudiants > 0)
                                    <a href="{{ route('etudiants.byClasse', $classe->id) }}" class="btn-action btn-view" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="text-muted">Aucun étudiant</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Cliquer pour choisir un fichier';
            document.getElementById('fileName').textContent = fileName;
        });
    </script>
@endpush
