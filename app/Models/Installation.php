<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code_installation',
        'nom',
        'type_profil',
        'statut',
        'criticite',
        'proprietaire_interne_id',
        'client_id',
        'equipement_principal_id',
    ];

    public function documents()
    {
        return $this->hasMany(DocumentInstallation::class);
    }

    public function historiqueStatuts()
    {
        return $this->hasMany(HistoriqueStatutInstallation::class);
    }

    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'lien_equipement_installation')->withPivot('role')->withTimestamps();
    }

    public function profilCatLab()
    {
        return $this->hasOne(ProfilCatLab::class);
    }

    public function profilIrm()
    {
        return $this->hasOne(ProfilIrm::class);
    }
}
