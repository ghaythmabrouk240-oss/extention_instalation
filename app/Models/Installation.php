<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use SoftDeletes;

    public const TYPE_IRM = 'IRM';
    public const TYPE_CATHETERISME = 'CATHETERISME';

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

    public static function profileTypes(): array
    {
        return [
            self::TYPE_IRM,
            self::TYPE_CATHETERISME,
        ];
    }

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
        return $this->hasOne(ProfilIRM::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
