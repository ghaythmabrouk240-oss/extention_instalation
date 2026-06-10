<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilCatLab extends Model
{
    protected $fillable = [
        'installation_id',
        'departement',
        'batiment',
        'etage',
        'systeme_angiographie',
        'station_controle',
        'radioprotection',
        'injecteur',
        'moniteurs',
        'controle_acces',
        'table_patient',
        'alimentation',
        'reseau',
        'ventilation',
        'protection_murale',
        'stockage_consommables',
        'signalisation_rayonnement',
        'conformite_salle_interventionnelle',
        'dispositifs_securite',
    ];

    protected $casts = [
        'controle_acces' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
