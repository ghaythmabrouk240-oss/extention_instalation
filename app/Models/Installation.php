<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function profilIRM()
    {
        return $this->hasOne(ProfilIRM::class);
    }
}
