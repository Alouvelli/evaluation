@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 m-5">
            @if (session('status'))
                <div class="alert {{ str_contains(session('status'), 'Erreur') || str_contains(session('status'), 'Impossible') ? 'alert-danger' : 'alert-success' }}" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #041f4e">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i>
                        Étudiants - {{ $classe->niveau->libelle_niveau ?? '' }} {{ $classe->libelle }}
                    </h3>
                    <div>
                        <a href="{{ route('etudiants.import') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Actions groupées --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="badge badge-primary p-2">
                                {{ $etudiants->count() }} étudiant(s)
                            </span>
                            @php
                                $evalues = $etudiants->where('statut', 1)->count();
                                $nonEvalues = $etudiants->where('statut', 0)->count();
                            @endphp
                            <span class="badge badge-success p-2 ml-2">
                                {{ $evalues }} ont évalué
                            </span>
                            <span class="badge badge-warning p-2 ml-2">
                                {{ $nonEvalues }} en attente
                            </span>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($etudiants->count() > 0)
                                <form action="{{ route('etudiants.resetStatut') }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Réinitialiser le statut de tous les étudiants de cette classe ?')">
                                    @csrf
                                    <input type="hidden" name="classe" value="{{ $classe->id }}">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-redo mr-1"></i> Réinitialiser les statuts
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Tableau des étudiants --}}
                    @if($etudiants->count() > 0)
                        <div class="table-responsive">
                            <table id="tableEtudiants" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>Matricule</th>
                                        <th width="20%" class="text-center">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiants as $index => $etudiant)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <code>{{ substr($etudiant->matricule, 0, 3) }}-{{ substr($etudiant->matricule, 3, 2) }}-{{ substr($etudiant->matricule, 5) }}</code>
                                            </td>
                                            <td class="text-center">
                                                @if($etudiant->statut == 0)
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock mr-1"></i> En attente
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check mr-1"></i> A évalué
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Aucun étudiant dans cette classe.
                            <a href="{{ route('etudiants.import') }}">Importer des étudiants</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#tableEtudiants').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "buttons": ["copy", "excel", "pdf"]
        }).buttons().container().appendTo('#tableEtudiants_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
@endsection
