<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SousEquipement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'identifiant',
        'designation',
        'marque',
        'modele',
        'description',
        'equipement_id',
    ];

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }
}
