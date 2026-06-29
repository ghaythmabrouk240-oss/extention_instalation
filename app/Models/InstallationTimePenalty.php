<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationTimePenalty extends Model
{
    protected $fillable = [
        'installation_id',
        'date_limite_contractuelle',
        'jours_retard',
        'penalite_par_jour',
        'montant_penalite',
        'payeur',
        'raison_retard',
        'applicable',
    ];

    protected $casts = [
        'date_limite_contractuelle' => 'date',
        'penalite_par_jour' => 'decimal:2',
        'montant_penalite' => 'decimal:2',
        'applicable' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
