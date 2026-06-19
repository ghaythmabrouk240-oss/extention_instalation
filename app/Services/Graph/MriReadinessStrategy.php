<?php

namespace App\Services\Graph;

use App\Models\DocumentInstallation;
use App\Models\Equipement;
use App\Models\Installation;

class MriReadinessStrategy implements InstallationReadinessStrategy
{
    public function buildGraph(Installation $installation): array
    {
        $installation->loadMissing([
            'profilIrm',
            'equipementPrincipal.sousEquipements',
            'equipements.sousEquipements',
            'documents',
        ]);

        $nodes = [];
        $edges = [];

        $this->buildEquipmentNodes($installation, $nodes, $edges);
        $this->buildSafetyNodes($installation, $nodes, $edges);
        $this->buildDocumentNodes($installation, $nodes, $edges);
        $this->buildLinkedEquipmentNodes($installation, $nodes, $edges);

        return [
            'nodes' => $nodes,
            'edges' => $edges,
            'summary' => $this->calculateSummary($installation, $nodes, $edges),
        ];
    }

    private function buildEquipmentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $profile = $installation->profilIrm;
        $equipment = $installation->equipementPrincipal;
        $equipmentState = $equipment ? 'vert' : 'rouge';
        $magneticFieldState = filled($profile?->champ_magnetique) ? 'vert' : 'rouge';
        $technicalRoomState = filled($profile?->atelier) ? 'vert' : 'jaune';

        $nodes[] = [
            'id' => 'equip_principal',
            'label' => 'Systeme IRM',
            'type' => 'equipement_principal',
            'state' => $equipmentState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'equipements',
            'source_id' => $equipment?->id,
            'tooltip' => $equipment
                ? $this->equipmentTooltip($equipment)
                : 'Aucun equipement principal rattache - bloquant',
        ];

        $nodes[] = [
            'id' => 'champ_magnetique',
            'label' => 'Champ magnetique',
            'type' => 'composant_profil',
            'state' => $magneticFieldState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => filled($profile?->champ_magnetique)
                ? 'Puissance renseignee : '.$profile->champ_magnetique
                : 'Puissance du champ non renseignee - requis',
        ];

        $nodes[] = [
            'id' => 'local_technique',
            'label' => 'Local technique',
            'type' => 'composant_profil',
            'state' => $technicalRoomState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => filled($profile?->atelier)
                ? 'Local / atelier : '.$profile->atelier
                : 'Local technique non renseigne - verification recommandee',
        ];

        $edges[] = $this->edge(
            'equip_principal',
            'champ_magnetique',
            'caracterise_par',
            $this->worstState($equipmentState, $magneticFieldState),
            $equipmentState === 'rouge' || $magneticFieldState === 'rouge'
        );
        $edges[] = $this->edge(
            'equip_principal',
            'local_technique',
            'depend_de',
            $this->worstState($equipmentState, $technicalRoomState)
        );
    }

    private function buildSafetyNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $profile = $installation->profilIrm;
        $shieldingState = filled($profile?->blindage) ? 'vert' : 'rouge';
        $controlledZoneState = $profile?->zone_controlee ? 'vert' : 'rouge';
        $ferromagneticState = $profile?->confinement_ferromagnetique ? 'vert' : 'rouge';
        $emergencyStopState = $profile?->arret_urgence ? 'vert' : 'rouge';
        $locationState = $this->locationState($profile);

        $nodes[] = [
            'id' => 'blindage_rf',
            'label' => 'Blindage RF',
            'type' => 'securite',
            'state' => $shieldingState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => filled($profile?->blindage)
                ? 'Blindage renseigne : '.$profile->blindage
                : 'Blindage RF non renseigne - bloquant',
        ];

        $nodes[] = [
            'id' => 'zone_controlee',
            'label' => 'Zone controlee',
            'type' => 'securite',
            'state' => $controlledZoneState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => $profile?->zone_controlee
                ? 'Zone controlee confirmee'
                : 'Zone controlee non confirmee - bloquant',
        ];

        $nodes[] = [
            'id' => 'confinement_ferromagnetique',
            'label' => 'Confinement ferromagnetique',
            'type' => 'securite',
            'state' => $ferromagneticState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => $profile?->confinement_ferromagnetique
                ? 'Confinement ferromagnetique confirme'
                : 'Confinement ferromagnetique non confirme - bloquant',
        ];

        $nodes[] = [
            'id' => 'arret_urgence',
            'label' => 'Arret urgence',
            'type' => 'securite',
            'state' => $emergencyStopState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => $profile?->arret_urgence
                ? 'Arret urgence confirme'
                : 'Arret urgence non confirme - bloquant',
        ];

        $nodes[] = [
            'id' => 'localisation_salle',
            'label' => 'Localisation salle IRM',
            'type' => 'composite',
            'state' => $locationState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => $this->locationTooltip($profile),
        ];

        $safetyState = $this->worstState(
            $shieldingState,
            $controlledZoneState,
            $ferromagneticState,
            $emergencyStopState,
            $locationState
        );
        $nodes[] = [
            'id' => 'securite_irm',
            'label' => 'Readiness securite IRM',
            'type' => 'securite_composite',
            'state' => $safetyState,
            'profile' => Installation::TYPE_IRM,
            'source_table' => 'profil_irms',
            'source_id' => $profile?->id,
            'tooltip' => $safetyState === 'vert'
                ? 'Tous les controles de securite IRM sont confirmes'
                : 'Synthese du blindage, zonage, confinement, arret urgence et localisation',
        ];

        foreach ([
            ['blindage_rf', $shieldingState],
            ['zone_controlee', $controlledZoneState],
            ['confinement_ferromagnetique', $ferromagneticState],
            ['arret_urgence', $emergencyStopState],
            ['localisation_salle', $locationState],
        ] as [$source, $state]) {
            $edges[] = $this->edge(
                $source,
                'securite_irm',
                'consolide',
                $this->worstState($state, $safetyState),
                $state === 'rouge'
            );
        }

        $edges[] = $this->edge(
            'securite_irm',
            'equip_principal',
            'requis_pour_operation',
            $this->worstState($safetyState, $this->nodeState($nodes, 'equip_principal')),
            $safetyState === 'rouge'
        );
    }

    private function buildDocumentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $rules = [
            ['rapport_installation_generale', 'Rapport installation generale', 'Rapport installation generale', 'localisation_salle', true],
            ['rapport_reception', 'Rapport de reception', 'Rapport de reception', 'equip_principal', true],
            ['plan_prevention', 'Plan de prevention', 'Plan de prevention', 'securite_irm', true],
            ['rapport_tests', 'Rapport des tests', 'Rapport des tests', 'champ_magnetique', false],
        ];

        foreach ($rules as [$id, $label, $category, $target, $required]) {
            $document = $this->activeDocument($installation, $category);
            $state = $document ? 'vert' : ($required ? 'rouge' : 'jaune');

            $nodes[] = [
                'id' => $id,
                'label' => $label,
                'type' => 'document',
                'state' => $state,
                'profile' => Installation::TYPE_IRM,
                'source_table' => 'document_installations',
                'source_id' => $document?->id,
                'tooltip' => $document
                    ? 'Version '.$document->version.' active'
                    : ($required ? 'Absent - document IRM requis' : 'Absent - document recommande'),
            ];

            $edges[] = $this->edge(
                $id,
                $target,
                'document_requis',
                $state,
                $required && $state === 'rouge'
            );
        }
    }

    private function buildLinkedEquipmentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $equipment = collect([$installation->equipementPrincipal])
            ->filter()
            ->merge($installation->equipements)
            ->unique('id')
            ->values();

        foreach ($equipment as $item) {
            $parentId = $item->id === $installation->equipement_principal_id
                ? 'equip_principal'
                : 'equipement_secondaire_'.$item->id;

            if ($parentId !== 'equip_principal') {
                $nodes[] = [
                    'id' => $parentId,
                    'label' => $item->designation ?: $item->code,
                    'type' => 'equipement_secondaire',
                    'state' => 'vert',
                    'profile' => Installation::TYPE_IRM,
                    'source_table' => 'equipements',
                    'source_id' => $item->id,
                    'tooltip' => $this->equipmentTooltip($item),
                ];
                $edges[] = $this->edge($parentId, 'equip_principal', 'rattache_a', 'vert');
            }

            foreach ($item->sousEquipements as $subEquipment) {
                $state = filled($subEquipment->identifiant) && filled($subEquipment->designation)
                    ? 'vert'
                    : 'jaune';
                $nodeId = 'sous_equipement_'.$subEquipment->id;

                $nodes[] = [
                    'id' => $nodeId,
                    'label' => $subEquipment->designation ?: $subEquipment->identifiant,
                    'type' => 'sous_equipement',
                    'state' => $state,
                    'profile' => Installation::TYPE_IRM,
                    'source_table' => 'sous_equipements',
                    'source_id' => $subEquipment->id,
                    'tooltip' => trim(
                        ($subEquipment->identifiant ?: 'Sans identifiant')
                        .' - '
                        .($subEquipment->description ?: 'Description non renseignee')
                    ),
                ];
                $edges[] = $this->edge($nodeId, $parentId, 'compose', $state);
            }
        }
    }

    private function calculateSummary(Installation $installation, array $nodes, array $edges): array
    {
        $totalNodes = count($nodes);
        $greens = count(array_filter($nodes, fn (array $node) => $node['state'] === 'vert'));

        return [
            'installation' => $installation->code_installation,
            'profile' => Installation::TYPE_IRM,
            'total_nodes' => $totalNodes,
            'blockers' => count(array_filter($edges, fn (array $edge) => $edge['blocking'] === true)),
            'warnings' => count(array_filter($nodes, fn (array $node) => $node['state'] === 'jaune')),
            'completion_rate' => $totalNodes > 0 ? round(($greens / $totalNodes) * 100) : 0,
        ];
    }

    private function locationState($profile): string
    {
        $values = [$profile?->batiment, $profile?->etage, $profile?->zone];
        $filledCount = count(array_filter($values, fn ($value) => filled($value)));

        return match ($filledCount) {
            3 => 'vert',
            0 => 'rouge',
            default => 'jaune',
        };
    }

    private function locationTooltip($profile): string
    {
        if (! $profile) {
            return 'Profil IRM absent - localisation non renseignee';
        }

        $parts = array_filter([
            filled($profile->batiment) ? 'Batiment '.$profile->batiment : null,
            filled($profile->etage) ? 'Etage '.$profile->etage : null,
            filled($profile->zone) ? 'Zone '.$profile->zone : null,
        ]);

        return $parts === [] ? 'Localisation non renseignee' : implode(' - ', $parts);
    }

    private function equipmentTooltip(Equipement $equipment): string
    {
        $identity = $equipment->designation ?: ($equipment->code ?: 'Equipement');
        $details = array_filter([$equipment->marque, $equipment->modele, $equipment->numero_serie]);

        return $details === [] ? $identity : $identity.' - '.implode(' / ', $details);
    }

    private function activeDocument(Installation $installation, string $category): ?DocumentInstallation
    {
        return $installation->documents
            ->where('categorie', $category)
            ->where('est_version_active', true)
            ->whereIn('profil_concerne', ['COMMUN', Installation::TYPE_IRM])
            ->sortByDesc('version')
            ->first();
    }

    private function edge(
        string $source,
        string $target,
        string $relation,
        string $state,
        bool $blocking = false
    ): array {
        return [
            'source' => $source,
            'target' => $target,
            'relation' => $relation,
            'state' => $state,
            'blocking' => $blocking,
        ];
    }

    private function nodeState(array $nodes, string $id): string
    {
        foreach ($nodes as $node) {
            if ($node['id'] === $id) {
                return $node['state'];
            }
        }

        return 'jaune';
    }

    private function worstState(string ...$states): string
    {
        if (in_array('rouge', $states, true)) {
            return 'rouge';
        }

        if (in_array('jaune', $states, true)) {
            return 'jaune';
        }

        return 'vert';
    }
}
