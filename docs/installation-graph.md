# Installation Graph Extension

## Overview

This extension provides a visual graph representation of installation readiness for the GMAO system. It displays the current state of an installation's components, safety measures, and documents in an interactive graph format.

## API Contract

### Endpoint

```
GET /dashboard/installation-graph?installation_id={id}&profile={profile}
```

### Parameters

- `installation_id` (required, integer): The ID of the installation
- `profile` (required, string): The profile type (`CATHETERISME` or `IRM`)

### Response Schema

```json
{
  "nodes": [
    {
      "id": "string",
      "label": "string",
      "type": "string",
      "state": "vert|jaune|rouge",
      "profile": "CATHETERISME|IRM",
      "source_table": "string",
      "source_id": "integer|null",
      "tooltip": "string"
    }
  ],
  "edges": [
    {
      "source": "string",
      "target": "string",
      "relation": "string",
      "state": "vert|jaune|rouge",
      "blocking": "boolean"
    }
  ],
  "summary": {
    "installation": "string",
    "profile": "CATHETERISME|IRM",
    "total_nodes": "integer",
    "blockers": "integer",
    "warnings": "integer",
    "completion_rate": "integer"
  }
}
```

### State Colors

- **vert**: Ready/valid
- **jaune**: To verify/incomplete
- **rouge**: Missing/blocking

## Architecture

### Backend Components

#### 1. InstallationReadinessStrategy Interface

Location: `app/Services/Graph/InstallationReadinessStrategy.php`

Interface defining the contract for profile-specific readiness strategies.

```php
public function buildGraph(Installation $installation): array
```

#### 2. CathLabReadinessStrategy

Location: `app/Services/Graph/CathLabReadinessStrategy.php`

Implements the 12 readiness rules for Cathétérisme profile:

1. **Equipment Principal**: State based on `equipement_principal_id` and `acceptance_test_status`
2. **Table Patient**: Required component, state based on `table_patient` field
3. **Injecteur**: Required component, state based on `injecteur` field
4. **Moniteurs**: Optional component, state based on `moniteurs` field
5. **Hemodynamic System**: Optional component, state based on `moniteurs` field
6. **Radioprotection**: Composite node from `radiation_shielding_status` and `lead_glass_status`
7. **Ceiling Support**: State based on `ceiling_support_status`
8. **Dose Monitoring**: State based on `dose_monitoring_available`
9. **Emergency Equipment**: State based on `emergency_equipment_status`
10. **Access Control**: State based on `access_control_status`
11. **Salle Interventionnelle**: Composite node with worst state among safety dependencies
12. **Documents**: 
    - Rapport de réception (blocking)
    - Rapport radioprotection (recommended)
    - Plan de prévention (recommended)
    - Rapport qualité (recommended)

#### 3. MriReadinessStrategy

Location: `app/Services/Graph/MriReadinessStrategy.php`

Stub implementation for IRM profile. Returns empty structure with TODO comment for future implementation by Person A.

#### 4. ReadinessStrategyFactory

Location: `app/Services/Graph/ReadinessStrategyFactory.php`

Factory class that resolves the appropriate strategy based on profile type.

```php
ReadinessStrategyFactory::make('CATHETERISME'); // Returns CathLabReadinessStrategy
ReadinessStrategyFactory::make('IRM'); // Returns MriReadinessStrategy
```

#### 5. InstallationGraphController

Location: `app/Http/Controllers/InstallationGraphController.php`

Controller handling the graph API endpoint with authorization checks.

#### 6. InstallationGraphRequest

Location: `app/Http/Requests/InstallationGraphRequest.php`

Form request validating:
- `installation_id`: required, integer, exists in installations table
- `profile`: required, in: CATHETERISME,IRM

### Frontend Components

#### Graph View

Location: `resources/views/installations/graph.blade.php`

Features:
- Installation selector dropdown
- Profile switch (Cathétérisme/IRM)
- State filter (All/Vert/Jaune/Rouge)
- Type filter (All/Equipment/Component/Safety/Document)
- Interactive graph using vis-network library
- Legend with state colors
- Summary statistics (total nodes, blockers, warnings, completion rate)
- Navigation on node click to related entities

### Database Changes

#### Migration

Location: `database/migrations/2026_06_17_105510_add_cathlab_readiness_fields_to_profil_cat_labs_table.php`

Added columns to `profil_cat_labs` table:
- `angio_manufacturer` (string, nullable)
- `angio_model` (string, nullable)
- `angio_serial` (string, nullable)
- `radiation_shielding_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `lead_glass_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `ceiling_support_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `emergency_equipment_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `access_control_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `dose_monitoring_available` (boolean, default: false)
- `hvac_info` (string, nullable)
- `acceptance_test_status` (enum: conforme, a_verifier, non_conforme, nullable)
- `installation_date` (date, nullable)
- `warranty_end_date` (date, nullable)

## Schema Discrepancies

The following discrepancies exist between the PRD and actual database schema:

### Table Names
- PRD: `installation_cath_profiles` → Actual: `profil_cat_labs`
- PRD: `installation_mri_profiles` → Actual: `profil_irms`
- PRD: `installation_equipment_links` → Actual: `lien_equipement_installation`
- PRD: `installation_documents` → Actual: `document_installations`
- PRD: `installation_status_histories` → Actual: `historique_statut_installations`

### Column Names (installations table)
- PRD: `installation_code` → Actual: `code_installation`
- PRD: `profile_type` → Actual: `type_profil`
- PRD: `main_equipement_id` → Actual: `equipement_principal_id`
- PRD: `internal_owner_id` → Actual: `proprietaire_interne_id`

### Column Names (profil_cat_labs table)
- PRD: `patient_table_info` → Actual: `table_patient`
- PRD: `injector_info` → Actual: `injecteur`
- PRD: `hemodynamic_system` → Actual: `moniteurs`

These discrepancies are documented here rather than corrected to maintain compatibility with existing code.

## Testing

### Unit Tests

Location: `tests/Unit/CathLabReadinessStrategyTest.php`

Tests cover:
- Equipment principal state variations (vert/jaune/rouge)
- Required components (table_patient, injecteur)
- Optional components (moniteurs)
- Safety components (radioprotection, ceiling_support, dose_monitoring)
- Composite node rules (salle_interventionnelle worst state)
- Document states (blocking vs recommended)
- Summary calculation

### Feature Tests

Location: `tests/Feature/InstallationGraphControllerTest.php`

Tests cover:
- Complete Cathétérisme installation (200, completion_rate=100, blockers=0)
- Installation without rapport_reception (200, blockers=1, rouge node)
- IRM profile stub (200, empty structure)
- Non-existent installation (404)
- Authorization failure (403)

## IRM Implementation Guide

For Person A to implement the IRM profile:

1. Update `app/Services/Graph/MriReadinessStrategy.php`
2. Implement the `buildGraph` method with IRM-specific readiness rules
3. Add IRM-specific nodes and edges based on IRM profile fields in `profil_irms` table
4. Consider IRM-specific components:
   - Magnetic field (champ_magnetique)
   - RF shielding (blindage)
   - Controlled zone (zone_controlee)
   - Ferromagnetic confinement (confinement_ferromagnetique)
   - Emergency stop (arret_urgence)
5. Follow the same pattern as CathLabReadinessStrategy for consistency
6. Add corresponding unit tests
7. Update documentation with IRM-specific rules

## Routes

- `GET /installations/graph` - Graph view page (requires auth)
- `GET /dashboard/installation-graph` - Graph API endpoint (requires auth)

## Access

The graph API uses the existing `InstallationPolicy::view` for authorization. Users must have permission to view installation KPIs to access the graph.

## Performance

The implementation is optimized for:
- < 2 seconds response time for up to 30 nodes
- Efficient database queries using eager loading
- Minimal client-side processing with vis-network

## Future Enhancements

- Add sub-equipment nodes when `sousequipements` table is available
- Implement IRM profile readiness rules
- Add export functionality for graph data
- Implement graph history/comparison
- Add real-time updates for installation status changes
