<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use HasFactory;

    protected $table = 'niveaux';
    protected $primaryKey = 'id_niveau';
    
    protected $fillable = [
        'libelle_niveau',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'id_niveau', 'id_niveau');
    }
}
