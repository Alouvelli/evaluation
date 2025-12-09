<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    use HasFactory;

    protected $table = 'etudiants';
    
    protected $fillable = [
        'matricule',
        'statut',
        'id_classe',
        'campus_id',
    ];

    protected $casts = [
        'matricule' => 'string',
        'statut' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants - Statuts
    |--------------------------------------------------------------------------
    */

    const STATUT_INACTIF = 0;
    const STATUT_ACTIF = 1;
    const STATUT_A_EVALUE = 2;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'id_classe');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class, 'id_etudiant');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActifs($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function scopeByClasse($query, int $classeId)
    {
        return $query->where('id_classe', $classeId);
    }

    public function scopeByCampus($query, int $campusId)
    {
        return $query->where('campus_id', $campusId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'étudiant a déjà évalué un cours
     */
    public function aDejaEvalue(): bool
    {
        return $this->statut === self::STATUT_A_EVALUE;
    }

    /**
     * Marque l'étudiant comme ayant évalué
     */
    public function marquerCommeEvalue(): bool
    {
        return $this->update(['statut' => self::STATUT_A_EVALUE]);
    }
}
