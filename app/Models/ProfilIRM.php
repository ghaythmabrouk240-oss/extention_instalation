<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilIRM extends Model
{
    protected $table = 'profil_irms';
    
    protected $guarded = [];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }
}
