<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilCatLab extends Model
{
    use HasFactory;
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
        'angio_manufacturer',
        'angio_model',
        'angio_serial',
        'radiation_shielding_status',
        'lead_glass_status',
        'ceiling_support_status',
        'emergency_equipment_status',
        'access_control_status',
        'dose_monitoring_available',
        'hvac_info',
        'acceptance_test_status',
        'installation_date',
        'warranty_end_date',
    ];

    protected $casts = [
        'controle_acces' => 'boolean',
        'dose_monitoring_available' => 'boolean',
        'installation_date' => 'date',
        'warranty_end_date' => 'date',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
