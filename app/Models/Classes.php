<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';
    
    protected $fillable = [
        'libelle',
        'campus_id',
        'id_niveau',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'id_classe');
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'id_classe');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByCampus($query, int $campusId)
    {
        return $query->where('campus_id', $campusId);
    }

    public function scopeByNiveau($query, int $niveauId)
    {
        return $query->where('id_niveau', $niveauId);
    }
}
