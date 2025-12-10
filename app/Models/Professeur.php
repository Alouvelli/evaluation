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
        'email',
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

    /**
     * Vérifie si le professeur a un email
     */
    public function hasEmail(): bool
    {
        return !empty($this->email);
    }

    /**
     * Obtenir les initiales du professeur
     */
    public function getInitialesAttribute(): string
    {
        $words = explode(' ', $this->full_name);
        $initiales = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initiales .= strtoupper(substr($word, 0, 1));
            }
        }
        return substr($initiales, 0, 2);
    }
}
