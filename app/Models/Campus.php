<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campus';
    
    protected $fillable = [
        'nomCampus',
    ];

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'campus_id');
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'campus_id');
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'campus_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'campus_id');
    }
}
