<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaires';
    
    protected $fillable = [
        'commentaire',
        'id_etudiant',
        'id_professeur',
        'id_cours',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByProfesseur($query, int $professeurId)
    {
        return $query->where('id_professeur', $professeurId);
    }

    public function scopeByCours($query, int $coursId)
    {
        return $query->where('id_cours', $coursId);
    }

    public function scopeNonVides($query)
    {
        return $query->whereNotNull('commentaire')->where('commentaire', '!=', '');
    }
}
