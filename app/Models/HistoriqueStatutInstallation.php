<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueStatutInstallation extends Model
{
    protected $fillable = [
        'installation_id',
        'user_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
