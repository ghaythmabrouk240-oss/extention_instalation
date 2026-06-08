<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentInstallation extends Model
{
    protected $fillable = [
        'installation_id',
        'categorie',
        'version',
        'statut',
        'est_bloquant',
    ];

    protected $casts = [
        'est_bloquant' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
