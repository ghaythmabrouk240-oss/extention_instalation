<?php

namespace App\Models;

use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'numero_equipement',
        'modele',
        'marque',
        'designation',
        'numero_serie',
        'modalite_id',
        'client_id',
        'software',
        'date_installation',
        'date_debut_garantie',
        'plan_prev',
        'garantie',
        'date_fin_garantie',
        'statut_couverture',
        'contrat_reference',
    ];

    protected $casts = [
        'date_installation' => 'date',
        'date_debut_garantie' => 'date',
        'date_fin_garantie' => 'date',
    ];

    public function sousEquipements()
    {
        return $this->hasMany(SousEquipement::class);
    }

    public function installations()
    {
        return $this->belongsToMany(Installation::class, 'lien_equipement_installation')->withPivot('role')->withTimestamps();
    }

    /**
     * Get QR code URL for this equipment
     *
     * @param int $size
     * @return string
     */
    public function getQrCodeUrl(int $size = 200): string
    {
        return app(QrCodeService::class)->generateEquipmentQrUrl($this->id, $size);
    }
}
