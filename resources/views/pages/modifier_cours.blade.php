@extends('layouts.admin')

@section('title', 'Modification du cours')

@section('content')
    <div class="card" style="max-width: 700px;">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Modification du cours</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('update_cours') }}" class="form-modern">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-book"></i> Nom du cours <span class="required">*</span>
                    </label>
                    <input type="text" class="form-input" name="libelle_cours" value="{{ $cours->libelle_cours }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users"></i> Classe <span class="required">*</span>
                    </label>
                    <select class="form-select" name="id_classe" id="id_classe" required>
                        <option value="{{ $cours->id_classe }}">{{ $cours->libelle }} - {{ $cours->libelle_niveau }}</option>
                        @foreach(\App\Http\Controllers\ClassesController::getListClasse() as $c)
                            @if($c->id != $cours->id)
                                <option value="{{ $c->id }}">{{ $c->libelle }} - {{ $c->libelle_niveau }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-chalkboard-teacher"></i> Professeur <span class="required">*</span>
                    </label>
                    <select class="form-select" name="id_professeur" id="id_professeur" required>
                        <option value="{{ $cours->id_professeur }}">{{ $cours->full_name }}</option>
                        @foreach(\App\Http\Controllers\ProfesseursController::getListProfs() as $c)
                            @if($c->id != $cours->id_professeur)
                                <option value="{{ $c->id }}">{{ $c->full_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="id_cours" value="{{ $cours->id_cours }}">

                <div class="form-actions">
                    <a href="{{ route('tools') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
