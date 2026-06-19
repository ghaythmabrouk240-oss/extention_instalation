<?php

namespace App\Services\Graph;

use App\Models\Installation;

class CathLabReadinessStrategy implements InstallationReadinessStrategy
{
    public function buildGraph(Installation $installation): array
    {
        $profile = $installation->profilCatLab;
        
        $nodes = [];
        $edges = [];
        
        // Build equipment nodes
        $this->buildEquipmentNodes($installation, $nodes, $edges);
        
        // Build safety nodes
        $this->buildSafetyNodes($installation, $nodes, $edges);
        
        // Build document nodes
        $this->buildDocumentNodes($installation, $nodes, $edges);
        
        // Build sub-equipment nodes
        $this->buildSubEquipmentNodes($installation, $nodes, $edges);
        
        // Calculate summary
        $summary = $this->calculateSummary($installation, $nodes, $edges);
        
        return [
            'nodes' => $nodes,
            'edges' => $edges,
            'summary' => $summary,
        ];
    }
    
    private function buildEquipmentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $profile = $installation->profilCatLab;
        
        // Rule 1: equip_principal (Système angiographie)
        $equipPrincipalState = $this->getEquipPrincipalState($installation);
        $nodes[] = [
            'id' => 'equip_principal',
            'label' => 'Système angiographie',
            'type' => 'equipement_principal',
            'state' => $equipPrincipalState,
            'profile' => 'CATHETERISME',
            'source_table' => 'equipements',
            'source_id' => $installation->equipement_principal_id,
            'tooltip' => $this->getEquipPrincipalTooltip($installation),
        ];
        
        // Rule 2: table_patient (composant obligatoire)
        $tablePatientState = filled($profile->table_patient ?? null) ? 'vert' : 'rouge';
        $nodes[] = [
            'id' => 'table_patient',
            'label' => 'Table patient',
            'type' => 'composant_profil',
            'state' => $tablePatientState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => filled($profile->table_patient ?? null) ? 'Renseignée' : 'Non renseignée (requis)',
        ];
        
        // Rule 2: injecteur (composant obligatoire)
        $injecteurState = filled($profile->injecteur ?? null) ? 'vert' : 'rouge';
        $nodes[] = [
            'id' => 'injecteur',
            'label' => 'Injecteur',
            'type' => 'composant_profil',
            'state' => $injecteurState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => filled($profile->injecteur ?? null) ? 'Renseigné' : 'Non renseigné (requis)',
        ];
        
        // Rule 3: moniteurs (composant optionnel)
        $moniteursState = filled($profile->moniteurs ?? null) ? 'vert' : 'jaune';
        $nodes[] = [
            'id' => 'moniteurs',
            'label' => 'Moniteurs',
            'type' => 'composant_profil',
            'state' => $moniteursState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => filled($profile->moniteurs ?? null) ? 'Renseigné' : 'Optionnel — non renseigné',
        ];
        
        // Rule 3: hemodynamic_system (composant optionnel) - mapped to moniteurs in actual schema
        // Note: PRD mentions hemodynamic_system but actual schema has moniteurs
        // Using moniteurs field for this component
        $hemodynamicState = filled($profile->moniteurs ?? null) ? 'vert' : 'jaune';
        $nodes[] = [
            'id' => 'hemodynamique',
            'label' => 'Système hémodynamique',
            'type' => 'composant_profil',
            'state' => $hemodynamicState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => filled($profile->moniteurs ?? null) ? 'Renseigné' : 'Optionnel — non renseigné',
        ];
        
        // Build edges from equip_principal
        $edges[] = [
            'source' => 'equip_principal',
            'target' => 'table_patient',
            'relation' => 'rattache_a',
            'state' => $equipPrincipalState,
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'equip_principal',
            'target' => 'injecteur',
            'relation' => 'requis_pour_operation',
            'state' => min($equipPrincipalState, $injecteurState),
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'equip_principal',
            'target' => 'moniteurs',
            'relation' => 'requis_pour_operation',
            'state' => min($equipPrincipalState, $moniteursState),
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'equip_principal',
            'target' => 'hemodynamique',
            'relation' => 'requis_pour_operation',
            'state' => min($equipPrincipalState, $hemodynamicState),
            'blocking' => false,
        ];
    }
    
    private function buildSafetyNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        $profile = $installation->profilCatLab;
        
        // Rule 4: radioprotection (noeud composite)
        $radioprotectionState = $this->getRadioprotectionState($profile);
        $nodes[] = [
            'id' => 'radioprotection',
            'label' => 'Radioprotection',
            'type' => 'securite_composite',
            'state' => $radioprotectionState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => $this->getRadioprotectionTooltip($profile),
        ];
        
        // Rule 5: ceiling_support
        $ceilingSupportState = $this->mapEnumToState($profile->ceiling_support_status ?? null);
        $nodes[] = [
            'id' => 'ceiling_support',
            'label' => 'Support plafond',
            'type' => 'securite',
            'state' => $ceilingSupportState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => $this->mapEnumToTooltip($profile->ceiling_support_status ?? null),
        ];
        
        // Rule 6: dose_monitoring
        $doseMonitoringState = ($profile->dose_monitoring_available ?? false) ? 'vert' : 'jaune';
        $nodes[] = [
            'id' => 'dose_monitoring',
            'label' => 'Dosimétrie',
            'type' => 'securite',
            'state' => $doseMonitoringState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => ($profile->dose_monitoring_available ?? false) ? 'Disponible' : 'Non disponible (recommandé)',
        ];
        
        // Rule 7: emergency_equip
        $emergencyEquipState = $this->mapEnumToState($profile->emergency_equipment_status ?? null);
        $nodes[] = [
            'id' => 'emergency_equip',
            'label' => 'Arrêt urgence',
            'type' => 'securite',
            'state' => $emergencyEquipState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => $this->mapEnumToTooltip($profile->emergency_equipment_status ?? null),
        ];
        
        // Rule 8: access_control
        $accessControlState = $this->mapEnumToState($profile->access_control_status ?? null);
        $nodes[] = [
            'id' => 'access_control',
            'label' => 'Contrôle d\'accès',
            'type' => 'securite',
            'state' => $accessControlState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => $this->mapEnumToTooltip($profile->access_control_status ?? null),
        ];
        
        // Rule 9: salle_interventionnelle (noeud composite dérivé)
        $salleInterventionnelleState = $this->getSalleInterventionnelleState([
            $radioprotectionState,
            $ceilingSupportState,
            $doseMonitoringState,
            $emergencyEquipState,
            $accessControlState,
        ]);
        $nodes[] = [
            'id' => 'salle_interventionnelle',
            'label' => 'Conformité salle interventionnelle',
            'type' => 'composite',
            'state' => $salleInterventionnelleState,
            'profile' => 'CATHETERISME',
            'source_table' => 'profil_cat_labs',
            'source_id' => $profile->id ?? null,
            'tooltip' => 'Agrégation des contrôles de sécurité',
        ];
        
        // Build safety edges
        $edges[] = [
            'source' => 'radioprotection',
            'target' => 'ceiling_support',
            'relation' => 'compose',
            'state' => min($radioprotectionState, $ceilingSupportState),
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'radioprotection',
            'target' => 'salle_interventionnelle',
            'relation' => 'consolide',
            'state' => min($radioprotectionState, $salleInterventionnelleState),
            'blocking' => $radioprotectionState === 'rouge',
        ];
        
        $edges[] = [
            'source' => 'dose_monitoring',
            'target' => 'salle_interventionnelle',
            'relation' => 'consolide',
            'state' => min($doseMonitoringState, $salleInterventionnelleState),
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'emergency_equip',
            'target' => 'salle_interventionnelle',
            'relation' => 'consolide',
            'state' => min($emergencyEquipState, $salleInterventionnelleState),
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'access_control',
            'target' => 'salle_interventionnelle',
            'relation' => 'consolide',
            'state' => min($accessControlState, $salleInterventionnelleState),
            'blocking' => false,
        ];
    }
    
    private function buildDocumentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        // Rule 11: rapport_reception (document bloquant)
        $rapportReceptionState = $this->hasActiveDocument($installation, 'Rapport de reception') ? 'vert' : 'rouge';
        $rapportReceptionDoc = $this->getActiveDocument($installation, 'Rapport de reception');
        $nodes[] = [
            'id' => 'rapport_reception',
            'label' => 'Rapport de réception',
            'type' => 'document',
            'state' => $rapportReceptionState,
            'profile' => 'CATHETERISME',
            'source_table' => 'document_installations',
            'source_id' => $rapportReceptionDoc?->id,
            'tooltip' => $rapportReceptionState === 'vert' 
                ? 'Version '.$rapportReceptionDoc->version.' active' 
                : 'Absent — bloquant pour passage Opérationnel',
        ];
        
        // Rule 12: rapport_radioprotection (document recommandé)
        $rapportRadioprotectionState = $this->hasActiveDocument($installation, 'Documents radioprotection') ? 'vert' : 'jaune';
        $rapportRadioprotectionDoc = $this->getActiveDocument($installation, 'Documents radioprotection');
        $nodes[] = [
            'id' => 'rapport_radioprotection',
            'label' => 'Rapport blindage rayonnement',
            'type' => 'document',
            'state' => $rapportRadioprotectionState,
            'profile' => 'CATHETERISME',
            'source_table' => 'document_installations',
            'source_id' => $rapportRadioprotectionDoc?->id,
            'tooltip' => $rapportRadioprotectionState === 'vert' 
                ? 'Version '.$rapportRadioprotectionDoc->version.' active' 
                : 'Absent (recommandé)',
        ];
        
        // Rule 12: plan_de_prevention (document recommandé)
        $planPreventionState = $this->hasActiveDocument($installation, 'Plan de prevention') ? 'vert' : 'jaune';
        $planPreventionDoc = $this->getActiveDocument($installation, 'Plan de prevention');
        $nodes[] = [
            'id' => 'plan_prevention',
            'label' => 'Plan de prévention',
            'type' => 'document',
            'state' => $planPreventionState,
            'profile' => 'CATHETERISME',
            'source_table' => 'document_installations',
            'source_id' => $planPreventionDoc?->id,
            'tooltip' => $planPreventionState === 'vert' 
                ? 'Version '.$planPreventionDoc->version.' active' 
                : 'Absent (recommandé)',
        ];
        
        // Rule 12: rapport_qualite (document recommandé)
        $rapportQualiteState = $this->hasActiveDocument($installation, 'Controle qualite') ? 'vert' : 'jaune';
        $rapportQualiteDoc = $this->getActiveDocument($installation, 'Controle qualite');
        $nodes[] = [
            'id' => 'rapport_qualite',
            'label' => 'Rapport qualité',
            'type' => 'document',
            'state' => $rapportQualiteState,
            'profile' => 'CATHETERISME',
            'source_table' => 'document_installations',
            'source_id' => $rapportQualiteDoc?->id,
            'tooltip' => $rapportQualiteState === 'vert' 
                ? 'Version '.$rapportQualiteDoc->version.' active' 
                : 'Absent (recommandé)',
        ];
        
        // Build document edges
        $edges[] = [
            'source' => 'rapport_reception',
            'target' => 'equip_principal',
            'relation' => 'document_requis',
            'state' => $rapportReceptionState,
            'blocking' => $rapportReceptionState === 'rouge',
        ];
        
        $edges[] = [
            'source' => 'rapport_radioprotection',
            'target' => 'radioprotection',
            'relation' => 'document_requis',
            'state' => $rapportRadioprotectionState,
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'plan_prevention',
            'target' => 'salle_interventionnelle',
            'relation' => 'document_requis',
            'state' => $planPreventionState,
            'blocking' => false,
        ];
        
        $edges[] = [
            'source' => 'rapport_qualite',
            'target' => 'salle_interventionnelle',
            'relation' => 'document_requis',
            'state' => $rapportQualiteState,
            'blocking' => false,
        ];
    }
    
    private function buildSubEquipmentNodes(Installation $installation, array &$nodes, array &$edges): void
    {
        // Rule 10: sous-équipements
        // Note: This requires the sousequipements table which is mentioned in PRD but not in current migrations
        // For now, we'll skip this as the table doesn't exist in the current schema
        // This can be added later when the sousequipements table is available
    }
    
    private function calculateSummary(Installation $installation, array $nodes, array $edges): array
    {
        $totalNodes = count($nodes);
        $blockers = count(array_filter($edges, fn($edge) => $edge['blocking'] === true));
        $warnings = count(array_filter($nodes, fn($node) => $node['state'] === 'jaune'));
        $greens = count(array_filter($nodes, fn($node) => $node['state'] === 'vert'));
        $completionRate = $totalNodes > 0 ? round(($greens / $totalNodes) * 100) : 0;
        
        return [
            'installation' => $installation->code_installation,
            'profile' => 'CATHETERISME',
            'total_nodes' => $totalNodes,
            'blockers' => $blockers,
            'warnings' => $warnings,
            'completion_rate' => $completionRate,
        ];
    }
    
    // Helper methods for state calculation
    
    private function getEquipPrincipalState(Installation $installation): string
    {
        $profile = $installation->profilCatLab;
        
        // Rule 1: equip_principal state
        if (blank($installation->equipement_principal_id)) {
            return 'rouge';
        }
        
        $acceptanceStatus = $profile->acceptance_test_status ?? null;
        
        if ($acceptanceStatus === 'conforme') {
            return 'vert';
        } elseif ($acceptanceStatus === 'a_verifier') {
            return 'jaune';
        } else {
            return 'rouge';
        }
    }
    
    private function getEquipPrincipalTooltip(Installation $installation): string
    {
        $equipment = $installation->mainEquipment;
        
        if (blank($installation->equipement_principal_id)) {
            return 'Non assigné';
        }
        
        if ($equipment) {
            $tooltip = $equipment->nom ?? 'Équipement inconnu';
            if (filled($equipment->numero_serie ?? null)) {
                $tooltip .= ' — n°série '.$equipment->numero_serie;
            }
            return $tooltip;
        }
        
        return 'Équipement introuvable';
    }
    
    private function getRadioprotectionState($profile): string
    {
        // Rule 4: radioprotection composite state
        $shieldingStatus = $profile->radiation_shielding_status ?? null;
        $leadGlassStatus = $profile->lead_glass_status ?? null;
        
        if ($shieldingStatus === 'non_conforme' || $leadGlassStatus === 'non_conforme') {
            return 'rouge';
        }
        
        if ($shieldingStatus === 'a_verifier' || $leadGlassStatus === 'a_verifier') {
            return 'jaune';
        }
        
        if ($shieldingStatus === 'conforme' && $leadGlassStatus === 'conforme') {
            return 'vert';
        }
        
        return 'jaune'; // Default if not set
    }
    
    private function getRadioprotectionTooltip($profile): string
    {
        $shieldingStatus = $profile->radiation_shielding_status ?? null;
        $leadGlassStatus = $profile->lead_glass_status ?? null;
        
        if ($shieldingStatus === 'conforme' && $leadGlassStatus === 'conforme') {
            return 'Blindage et vitres plombées conformes';
        }
        
        if ($shieldingStatus === 'non_conforme' || $leadGlassStatus === 'non_conforme') {
            return 'Non conforme — bloquant';
        }
        
        return 'À vérifier';
    }
    
    private function getSalleInterventionnelleState(array $states): string
    {
        // Rule 9: worst state among dependencies (rouge > jaune > vert)
        if (in_array('rouge', $states, true)) {
            return 'rouge';
        }
        
        if (in_array('jaune', $states, true)) {
            return 'jaune';
        }
        
        return 'vert';
    }
    
    private function mapEnumToState(?string $enumValue): string
    {
        return match ($enumValue) {
            'conforme' => 'vert',
            'a_verifier' => 'jaune',
            'non_conforme' => 'rouge',
            default => 'jaune', // Default if not set
        };
    }
    
    private function mapEnumToTooltip(?string $enumValue): string
    {
        return match ($enumValue) {
            'conforme' => 'Conforme',
            'a_verifier' => 'À vérifier',
            'non_conforme' => 'Non conforme',
            default => 'Non renseigné',
        };
    }
    
    private function hasActiveDocument(Installation $installation, string $category): bool
    {
        return $installation->documents()
            ->where('categorie', $category)
            ->where('est_version_active', true)
            ->exists();
    }
    
    private function getActiveDocument(Installation $installation, string $category)
    {
        return $installation->documents()
            ->where('categorie', $category)
            ->where('est_version_active', true)
            ->first();
    }
    
    private function minState(string $state1, string $state2): string
    {
        $priority = ['rouge' => 0, 'jaune' => 1, 'vert' => 2];
        $p1 = $priority[$state1] ?? 1;
        $p2 = $priority[$state2] ?? 1;
        return $p1 <= $p2 ? $state1 : $state2;
    }
}
