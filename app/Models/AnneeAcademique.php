<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'annee_academique';
    
    protected $fillable = [
        'annee1',
        'annee2',
    ];

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'annee_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne l'année académique formatée (ex: "2024-2025")
     */
    public function getLibelleAttribute(): string
    {
        return $this->annee1 . '-' . $this->annee2;
    }
}
