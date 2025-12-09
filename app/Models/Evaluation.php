<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';
    
    protected $fillable = [
        'id_professeur',
        'id_cours',
        'idQ',
        'note',
    ];

    protected $casts = [
        'note' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'idQ', 'idQ');
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

    public function scopeByQuestion($query, int $questionId)
    {
        return $query->where('idQ', $questionId);
    }
}
