<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cours extends Model
{
    use HasFactory;

    protected $table = 'cours';
    protected $primaryKey = 'id_cours';
    
    protected $fillable = [
        'etat',
        'libelle_cours',
        'id_classe',
        'id_professeur',
        'semestre',
        'campus_id',
        'annee_id',
    ];

    protected $casts = [
        'etat' => 'integer',
        'semestre' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants - États
    |--------------------------------------------------------------------------
    */

    const ETAT_INACTIF = 0;
    const ETAT_ACTIF = 1;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'id_classe');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'id_cours', 'id_cours');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class, 'id_cours', 'id_cours');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActifs($query)
    {
        return $query->where('etat', self::ETAT_ACTIF);
    }

    public function scopeBySemestre($query, int $semestre)
    {
        return $query->where('semestre', $semestre);
    }

    public function scopeByAnnee($query, int $anneeId)
    {
        return $query->where('annee_id', $anneeId);
    }

    public function scopeByClasse($query, int $classeId)
    {
        return $query->where('id_classe', $classeId);
    }

    public function scopeByProfesseur($query, int $professeurId)
    {
        return $query->where('id_professeur', $professeurId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Moyenne des notes pour ce cours
     */
    public function getMoyenneAttribute(): ?float
    {
        return $this->evaluations()->avg('note');
    }

    /**
     * Nombre d'évaluations pour ce cours
     */
    public function getNombreEvaluationsAttribute(): int
    {
        return $this->evaluations()->count();
    }
}
