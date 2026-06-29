<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationContractCoverage extends Model
{
    protected $fillable = [
        'installation_id',
        'equipement_id',
        'garantie_active',
        'date_fin_garantie',
        'contrat_renouvelable',
        'contrat_reference',
        'client_partie',
        'stiet_partie',
        'statut_couverture',
    ];

    protected $casts = [
        'date_fin_garantie' => 'date',
        'garantie_active' => 'boolean',
        'contrat_renouvelable' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }
}
