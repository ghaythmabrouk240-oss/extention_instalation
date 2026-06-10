<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilIRM extends Model
{
    protected $table = 'profil_irms';

    protected $fillable = [
        'installation_id',
        'champ_magnetique',
        'zone_controlee',
        'blindage',
        'atelier',
        'confinement_ferromagnetique',
        'arret_urgence',
        'batiment',
        'etage',
        'zone',
    ];

    protected $casts = [
        'zone_controlee' => 'boolean',
        'confinement_ferromagnetique' => 'boolean',
        'arret_urgence' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
