<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationBudget extends Model
{
    protected $fillable = [
        'installation_id',
        'regime_prise_en_charge',
        'devise',
        'budget_prevu',
        'total_frais',
        'total_penalites',
        'total_final',
        'statut_validation',
        'reference_contrat',
        'notes_finance',
    ];

    protected $casts = [
        'budget_prevu' => 'decimal:2',
        'total_frais' => 'decimal:2',
        'total_penalites' => 'decimal:2',
        'total_final' => 'decimal:2',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function expenses()
    {
        return $this->hasMany(InstallationExpense::class);
    }

    public function timePenalty()
    {
        return $this->hasOne(InstallationTimePenalty::class);
    }

    public function contractCoverages()
    {
        return $this->hasMany(InstallationContractCoverage::class);
    }
}
