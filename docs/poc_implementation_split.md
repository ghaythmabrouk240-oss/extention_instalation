# Module Gestion des Installations - POC Backlog and 2-Person Split

Date: 2026-06-09  
Context: Philips Healthcare / ST IET, Laravel POC, 1-month stage  
Sources checked: PRD.pdf, MVP Scope Freeze POC v1.1.4, ERD/use-case/state-machine diagrams, current Laravel codebase.

## 1. Product Position

The module must remain one module: `Installation` is the parent entity, with exactly one child profile:

- MRI / IRM profile
- Cath lab / Catheterisme profile

The POC is not a production integration, but the demo must be coherent:

- Create, list, show, edit, archive an installation.
- Select profile type and save the matching profile-specific fields.
- Link client/site, main equipment, secondary equipment, documents, and status history.
- Enforce simple internal permissions.
- Show basic KPIs and a monthly installation calendar if this is now expected for the demo.

## 2. Critical Gaps Found in Current Code

These are the main gaps between the PRD/POC and the current Laravel implementation.

1. Roles and permissions are not implemented.
   - `routes/web.php` exposes resource routes directly.
   - No role column, middleware, policy, gate, or permission matrix exists.
   - Current code uses `auth()->id() ?? 1`, which is useful for a demo fallback but not a real permission model.

2. Profile separation is incomplete.
   - `InstallationController@show` loads `profilCatLab` but not `profilIrm`.
   - Installation create/edit forms only save common fields.
   - There is a `ProfilCatLabController`, but no equivalent `ProfilIRMController`.
   - The user journey should not require creating the parent first and then manually opening a separate profile screen unless this is explicitly accepted as a POC simplification.

3. MRI and Cath fields are not balanced.
   - MRI migration exists, but fields are not exposed in installation forms or detail view.
   - CathLab has a controller, but its migration lacks a real foreign key and one-to-one uniqueness.
   - The PRD expects profile-specific required fields before validation/operational status.

4. Status workflow is not guarded.
   - Any status can currently be selected in edit.
   - The state machine requires role-based transitions and minimal conditions:
     Brouillon -> En validation -> Installe -> Operationnel -> En maintenance / Temporairement indisponible / Archive.
   - Archive should be admin-only.

5. Document attachment is too thin.
   - Current table stores category, version, status, blocking flag.
   - Missing: DMS reference or file reference, active version concept, profile concerned, required-document checklist, audit of critical replacement/deletion.
   - PRD allows table POC fallback if DMS is not quickly usable, so use a POC table but name the DMS gap clearly.

6. Dashboard KPIs are missing.
   - Root redirects to installations index.
   - PRD lists basic KPIs as Should Have:
     installations by profile, operational rate, missing blocking docs, missing main equipment, installations in maintenance.

7. Monthly installation calendar is missing.
   - POC scope says calendar is optional, but the latest request asks to add it.
   - Current `installations` table has no planned installation date fields. Equipment has `date_installation`, but that belongs to equipment, not installation planning.

8. Budget and time tracking are not proposed in the current tables.
   - Neither PRD nor MVP Scope Freeze makes financial budget or installation duration a core requirement.
   - Current migrations do not include budget or time spent fields.
   - If stakeholders expect this, add it as optional P2/P3 fields, not as a blocker for the main POC.

9. Filters and demo data need cleanup.
   - PRD/scope include simple filters.
   - Current index does `Installation::latest()->get()` with no search/filter.
   - `DummyDataSeeder` is inconsistent with the current equipment schema and can break demo setup.

10. Referential integrity is inconsistent.
    - Some migrations use `foreignId()->constrained()`, others use raw unsigned integers.
    - Profiles/documents/status/equipment links should not allow orphan rows in a credible POC.

## 3. Priority Backlog

### P0 - Must Fix for a Defensible POC

- Add simple role model:
  - `admin`
  - `biomedical`
  - `manager` optional, disabled or read-only by default
- Protect routes/actions with policies or middleware.
- Implement status transition service with role checks.
- Integrate MRI and Cath profile fields into installation create/edit/show flow.
- Ensure only one matching profile exists per installation.
- Add document attachment via POC table with DMS reference fields.
- Fix migrations/foreign keys/unique constraints.
- Fix demo seed data.
- Add simple filters on installation list.
- Document what is real, simulated, and deferred.

### P1 - Added/Expected Demo Value

- Basic KPI dashboard.
- Monthly installation calendar.
- Required document checklist by profile.
- Show missing documents in list/detail/dashboard.
- Add automated feature tests for create/update/profile/status/permissions.

### P2 - Optional Unless Philips Confirms

- Manager validation workflow.
- Export CSV/Excel.
- Budget fields:
  - `budget_prevu`
  - `budget_reel`
  - `devise`
- Time fields:
  - `temps_prevu_heures`
  - `temps_reel_heures`
  - `date_debut_prevue`
  - `date_fin_prevue`
  - `date_fin_reelle`
- Full DMS versioning integration.
- Audit log table beyond status/document events.
- Intervention table integration if the existing GMAO table is found.

## 4. Recommended Technical Decisions

### Role Management

Use the lightest implementation compatible with Laravel:

- Add `role` enum/string on users.
- Add `InstallationPolicy`.
- Add middleware or policies for route actions.
- Use constants/config for roles and permissions.
- Do not add external packages unless the existing GMAO already uses one.

Minimal permission matrix:

| Action | Admin | Biomedical | Manager optional |
| --- | --- | --- | --- |
| View list/detail | Yes | Yes | Yes |
| View KPIs/calendar | Yes | Yes | Yes if enabled |
| Create installation | Yes | Yes | No |
| Edit common fields | Yes | Yes | No |
| Edit profile fields | Yes | Yes | No |
| Link equipment | Yes | Yes | No |
| Attach/replace document | Yes | Yes | No |
| Operational status changes | Yes | Yes | Validation only if enabled |
| Archive installation | Yes | No | No |
| Manage users/roles | Yes | No | No |

### Profile Separation

Use one parent form with conditional profile sections:

- Common fields always shown.
- If `IRM`, save/update `profil_irms`.
- If `CATHETERISME`, save/update `profil_cat_labs`.
- When changing profile type, either block the change after creation or delete/recreate the previous child profile with confirmation. For POC, blocking after creation is safer.

### Document Attachment

If no DMS table is quickly available, extend the POC document table:

- `installation_id`
- `profile_type` nullable/common
- `categorie`
- `version`
- `statut`
- `est_bloquant`
- `dms_object_id` nullable
- `file_path` or `external_url` nullable
- `is_active`
- `uploaded_by`
- `replaced_document_id` nullable

### KPI Dashboard

Basic dashboard metrics:

- Total active installations.
- Count by profile: IRM vs Catheterisme.
- Count by status.
- Operational rate.
- Installations missing main equipment.
- Installations with blocking/missing required documents.
- Installations in maintenance.
- Installations planned this month.

### Monthly Calendar

Add installation-level planning dates:

- `planned_start_date`
- `planned_end_date`
- `actual_start_date` optional
- `actual_end_date` optional

Calendar can be a simple month grid or grouped list for the POC:

- Current month by default.
- Previous/next month navigation.
- Cards show code, name, profile, status, client/site.

## 5. Two-Person Split

Both people share the foundation and demo quality. Each person owns one profile end-to-end.

### Shared Foundation - Both People

Owner: both, pair for alignment.

- Agree final field names and status constants.
- Align migrations with PRD names or document the chosen local names.
- Decide how role is stored on `users`.
- Define permission matrix and status transition matrix.
- Fix seed/demo data so both MRI and Cath flows are testable.
- Keep a shared demo script:
  1. Admin/biomedical creates MRI installation.
  2. Admin/biomedical creates Cath installation.
  3. Attach main equipment and document.
  4. Move statuses through allowed transitions.
  5. Show KPI dashboard and monthly calendar.
  6. Show unauthorized action blocked.

### Person A - MRI + Permissions + KPI

Profile ownership: IRM/MRI.

Main responsibilities:

- Implement simple roles:
  - user `role` field
  - policy/middleware
  - admin vs biomedical vs manager optional checks
- Implement status transition service and guard archive as admin-only.
- Build MRI profile integration:
  - migration/model cleanup
  - validation rules
  - create/edit conditional fields
  - show tab for MRI
  - required documents for MRI
- Build KPI dashboard backend and view:
  - counts by profile/status
  - operational rate
  - maintenance count
  - missing main equipment
  - missing blocking documents
- Tests:
  - biomedical can create/update MRI
  - manager cannot edit
  - biomedical cannot archive
  - status transition rejects invalid jump
  - dashboard numbers match seeded data

Deliverable for Person A:

- MRI flow works from create to detail.
- Role checks are visible in UI and enforced server-side.
- Dashboard shows credible KPI values.

### Person B - Cath + Documents + Calendar

Profile ownership: Catheterisme/CathLab.

Main responsibilities:

- Build Cath profile integration:
  - migration/model cleanup
  - validation rules
  - create/edit conditional fields
  - show tab for Cath
  - required documents for Cath
- Implement document attachment POC:
  - DMS-compatible metadata fields
  - active version rule for blocking docs
  - document status display
  - missing required document detection support
- Improve equipment links:
  - main equipment display by relation, not raw ID only
  - secondary equipment pivot constraints
  - sub-equipment display through linked equipment
- Build monthly calendar:
  - add planning dates to installation
  - controller query by month
  - simple month/list view
- Tests:
  - Cath installation saves its child profile
  - wrong profile child cannot be attached
  - document can be attached and marked blocking
  - missing document logic works
  - calendar shows installations in selected month

Deliverable for Person B:

- Cath flow works from create to detail.
- Document attachment is defensible as DMS fallback.
- Calendar shows this month's planned installations.

## 6. Integration Checkpoints

Checkpoint 1 - Schema compiles:

- `php artisan migrate:fresh --seed` works.
- Both profile tables have one-to-one constraints.
- Demo users have roles.

Checkpoint 2 - Vertical slice:

- One MRI and one Cath installation can be created with profile fields.
- Detail page displays correct child profile.
- Status history is created.

Checkpoint 3 - Permissions:

- Admin can archive.
- Biomedical can create/edit but cannot archive.
- Manager can only view KPIs/calendar if enabled.

Checkpoint 4 - Demo completeness:

- Dashboard has real values.
- Calendar has month data.
- Document attachment works.
- Filters work.
- Known limitations are documented.

## 7. Budget and Time Verification

Answer to the requested check:

- Current tables do not propose financial budget fields.
- Current tables do not propose time spent / installation duration fields.
- PRD and POC scope do not make budget/time mandatory.
- Add them only if Philips confirms they are expected in the demo.

Recommended optional implementation:

- If used only for display/filtering: add nullable fields directly on `installations`.
- If used as tracked financial history: create separate `installation_financials` or `installation_effort_logs`.
- For this POC, direct nullable fields are enough.

## 8. Demo Definition of Done

The POC is demo-ready when:

- MRI and Cath installations are both created through the same parent flow.
- The detail page displays the correct profile section.
- Permissions block at least one unauthorized action visibly.
- Status transitions are controlled and historized.
- Documents are attached through DMS reference or POC table.
- Dashboard shows basic KPIs.
- Calendar shows installations planned in the selected month.
- Demo data loads cleanly.
- Limitations are written down instead of hidden.
