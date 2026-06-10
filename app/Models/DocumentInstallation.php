<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentInstallation extends Model
{
    protected $fillable = [
        'installation_id',
        'categorie',
        'type_rapport',
        'version',
        'statut',
        'description',
        'est_bloquant',
        'reference_dms',
        'reference_fichier',
        'fichier_path',
        'fichier_original_name',
        'fichier_mime_type',
        'profil_concerne',
        'est_version_active',
    ];

    protected $casts = [
        'est_bloquant' => 'boolean',
        'est_version_active' => 'boolean',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
