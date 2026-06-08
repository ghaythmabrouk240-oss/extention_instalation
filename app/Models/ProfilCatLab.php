<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilCatLab extends Model
{
    protected $fillable = [
        'installation_id',
        'systeme_angiographie',
        'radioprotection',
        'injecteur',
        'moniteurs',
        'controle_acces',
        'table_patient',
    ];

    protected $casts = [
        'controle_acces' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
