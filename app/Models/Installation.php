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
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'calendar_note',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
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

    public function equipementPrincipal()
    {
        return $this->belongsTo(Equipement::class, 'equipement_principal_id');
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

    public function proprietaireInterne()
    {
        return $this->belongsTo(User::class, 'proprietaire_interne_id');
    }

    public static function standardStatuses(): array
    {
        return [
            'Brouillon',
            'En validation',
            'Installe',
            'Operationnel',
            'En maintenance',
            'Temporairement indisponible',
            'Archive',
        ];
    }

    public static function cathLabRequiredDocumentCategories(): array
    {
        return [
            'Rapport de reception',
            'Controle qualite',
            'Documents radioprotection',
            'Plan de prevention',
            'Rapports techniques',
        ];
    }

    public static function irmRequiredDocumentCategories(): array
    {
        return [
            'Rapport d\'installation',
            'Rapport de reception',
            'Plan de salle',
            'Plan de prevention',
        ];
    }

    public function requiredDocumentCategories(): array
    {
        return match ($this->type_profil) {
            'CATHETERISME' => self::cathLabRequiredDocumentCategories(),
            'IRM' => self::irmRequiredDocumentCategories(),
            default => [],
        };
    }

    public function missingRequiredDocumentCategories(): array
    {
        $activeDocuments = $this->documents
            ->where('est_version_active', true)
            ->whereIn('profil_concerne', ['COMMUN', $this->type_profil])
            ->pluck('categorie')
            ->all();

        return array_values(array_diff($this->requiredDocumentCategories(), $activeDocuments));
    }

    public function activeDocumentByType(string $typeRapport): ?DocumentInstallation
    {
        return $this->documents
            ->where('type_rapport', $typeRapport)
            ->where('est_version_active', true)
            ->sortByDesc('version')
            ->first();
    }

    public function activeDocumentByCategorie(string $categorie): ?DocumentInstallation
    {
        return $this->documents
            ->where('categorie', $categorie)
            ->where('est_version_active', true)
            ->sortByDesc('version')
            ->first();
    }

    public static function uploadableReportCategories(): array
    {
        return [
            [
                'categorie' => 'Rapport installation generale',
                'type_rapport' => 'installation_generale',
                'icon' => 'fa-building',
                'description' => 'PV, identification salle, infrastructure et securite.',
            ],
            [
                'categorie' => 'Rapport des tests',
                'type_rapport' => 'rapport_tests',
                'icon' => 'fa-vial-circle-check',
                'description' => 'Tests qualite, securite et validation technique.',
            ],
            [
                'categorie' => 'Rapport de reception',
                'type_rapport' => 'document_requis',
                'icon' => 'fa-clipboard-check',
                'description' => 'Rapport de reception de la salle ou de l equipement.',
                'required' => true,
            ],
            [
                'categorie' => 'Controle qualite',
                'type_rapport' => 'document_requis',
                'icon' => 'fa-check-double',
                'description' => 'Controle qualite et conformite (PDF ou scan manuscrit).',
                'required' => true,
            ],
            [
                'categorie' => 'Documents radioprotection',
                'type_rapport' => 'document_requis',
                'icon' => 'fa-radiation',
                'description' => 'Documents radioprotection et mesures associees.',
                'required' => true,
            ],
            [
                'categorie' => 'Plan de prevention',
                'type_rapport' => 'document_requis',
                'icon' => 'fa-shield-halved',
                'description' => 'Plan de prevention et consignes de securite.',
                'required' => true,
            ],
            [
                'categorie' => 'Rapports techniques',
                'type_rapport' => 'rapport_technique',
                'icon' => 'fa-file-lines',
                'description' => 'Rapports techniques, fiches intervention et comptes rendus.',
                'required' => true,
            ],
        ];
    }
}
