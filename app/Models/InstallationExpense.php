<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationExpense extends Model
{
    protected $fillable = [
        'installation_id',
        'type_depense',
        'date_depense',
        'description',
        'fournisseur',
        'quantite',
        'montant_unitaire',
        'montant_total',
        'tva',
        'document_id',
        'created_by',
    ];

    protected $casts = [
        'date_depense' => 'date',
        'quantite' => 'decimal:2',
        'montant_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'tva' => 'decimal:2',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function document()
    {
        return $this->belongsTo(DocumentInstallation::class, 'document_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
