<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professeur extends Model
{
    use HasFactory;

    protected $table = 'professeurs';
    
    protected $fillable = [
        'full_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'id_professeur');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'id_professeur');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class, 'id_professeur');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Moyenne générale des évaluations du professeur
     */
    public function getMoyenneGeneraleAttribute(): ?float
    {
        return $this->evaluations()->avg('note');
    }
}
