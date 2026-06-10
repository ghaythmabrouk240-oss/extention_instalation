import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "outputs/installation_poc_checklist";
await fs.mkdir(outputDir, { recursive: true });

const repoSnapshot = {
  branch: "main",
  upstream: "origin/main",
  remote: "https://github.com/ghaythmabrouk240-oss/extention_instalation.git",
  status: "Local workspace has uncommitted changes; no pushed implementation detected for the new checklist tasks.",
  checkedAt: "2026-06-09",
};

const tasks = [
  ["Shared", "Foundation", "P0", "Agree final field names and status constants", "Common constants for profile types, statuses, roles, priorities, and document states are agreed and reused.", "Section 5 - Shared Foundation", "To Do", "No", "Current code uses scattered strings; needs shared constants.", "Both", "Sprint 1"],
  ["Shared", "Foundation", "P0", "Align migrations with PRD names or document local names", "Migrations either match the PRD vocabulary or local deviations are documented clearly.", "Section 5 - Shared Foundation", "Partial", "Partial", "Installation/profile migrations exist, but foreign keys and names are inconsistent.", "Both", "Sprint 1"],
  ["Shared", "Foundation", "P0", "Decide how role is stored on users", "A role field or equivalent internal mechanism exists for admin, biomedical, and optional manager.", "Section 5 - Shared Foundation", "To Do", "No", "No user role column, gate, policy, or middleware found.", "Both", "Sprint 1"],
  ["Shared", "Foundation", "P0", "Define permission matrix and status transition matrix", "The PRD permission matrix and state-machine transitions are encoded in a simple reference/service.", "Section 5 - Shared Foundation", "To Do", "No", "Routes are open resource routes; edit form allows any listed status.", "Both", "Sprint 1"],
  ["Shared", "Foundation", "P0", "Fix seed and demo data", "Seed data creates valid users, clients, equipment, MRI installation, Cath installation, documents, and statuses.", "Section 5 - Shared Foundation", "To Do", "No", "DummyDataSeeder is untracked and inconsistent with current equipment schema.", "Both", "Sprint 1"],
  ["Shared", "Foundation", "P0", "Maintain shared demo script", "Demo script covers create MRI, create Cath, attach equipment/document, transition status, show dashboard/calendar, and blocked action.", "Section 5 - Shared Foundation", "To Do", "No", "No runnable demo checklist/script in repo yet.", "Both", "Sprint 4"],
  ["Shared", "Integration", "P0", "Ensure php artisan migrate:fresh --seed works", "Fresh migration and seed run cleanly from zero database state.", "Section 6 - Checkpoint 1", "To Do", "No", "Migration constraints and seed mismatch need cleanup before this can be trusted.", "Both", "Sprint 1"],
  ["Shared", "Integration", "P0", "Add one-to-one profile constraints", "Each installation has at most one matching MRI or Cath child profile.", "Section 6 - Checkpoint 1", "Partial", "Partial", "MRI has constrained installation_id; Cath uses unsignedBigInteger and no uniqueness.", "Both", "Sprint 1"],
  ["Shared", "Integration", "P0", "Create demo users with roles", "Admin, biomedical, and optional manager demo accounts exist and are usable in tests/demo.", "Section 6 - Checkpoint 1", "To Do", "No", "Users exist through factory only; roles are not defined.", "Both", "Sprint 1"],
  ["Shared", "Documentation", "P0", "Document real, simulated, and deferred scope", "Limitations around DMS, manager role, budget/time, interventions, and production integration are visible.", "Section 3 - P0 Backlog", "Partial", "No", "Local planning docs exist but are untracked, not pushed.", "Both", "Sprint 4"],

  ["Person A", "Roles and Permissions", "P0", "Add simple role model", "User model supports admin, biomedical, and optional manager role values.", "Section 5 - Person A", "To Do", "No", "No role field exists in users migration/model.", "Person A", "Sprint 1"],
  ["Person A", "Roles and Permissions", "P0", "Add InstallationPolicy or middleware", "Server-side authorization protects create, edit, archive, document, status, KPI, and calendar actions.", "Section 4 - Role Management", "To Do", "No", "No policies/gates/middleware found for module actions.", "Person A", "Sprint 1"],
  ["Person A", "Roles and Permissions", "P0", "Implement admin vs biomedical vs manager checks", "Admin has full access; biomedical can create/edit but not archive; manager is read-only unless enabled.", "Section 4 - Permission Matrix", "To Do", "No", "No role-specific checks currently enforced.", "Person A", "Sprint 1"],
  ["Person A", "Status Workflow", "P0", "Implement status transition service", "Allowed transitions are checked against role and minimum conditions before update.", "Section 5 - Person A", "To Do", "No", "Status update currently happens through generic edit validation.", "Person A", "Sprint 2"],
  ["Person A", "Status Workflow", "P0", "Guard archive as admin-only", "Biomedical and manager cannot archive/delete; attempted archive is rejected and visible in tests.", "Section 5 - Person A", "To Do", "No", "Destroy route is exposed through resource route.", "Person A", "Sprint 2"],
  ["Person A", "MRI Profile", "P0", "Clean up MRI migration and model", "MRI profile table/model use consistent naming, relationship, casts, and one-to-one constraints.", "Section 5 - Person A", "Partial", "Partial", "profil_irms migration/model exists but is not integrated in parent flow.", "Person A", "Sprint 2"],
  ["Person A", "MRI Profile", "P0", "Add MRI validation rules", "MRI required fields block validation/operational status when missing.", "Section 5 - Person A", "To Do", "No", "Installation request validates common fields only.", "Person A", "Sprint 2"],
  ["Person A", "MRI Profile", "P0", "Add MRI fields to create/edit flow", "Selecting IRM displays and saves MRI-specific fields in the parent installation form.", "Section 5 - Person A", "To Do", "No", "Create/edit views only expose common installation fields.", "Person A", "Sprint 2"],
  ["Person A", "MRI Profile", "P0", "Display MRI tab on detail page", "Detail page shows MRI profile values when installation type is IRM.", "Section 5 - Person A", "To Do", "No", "Show page loads/displays Cath profile only.", "Person A", "Sprint 2"],
  ["Person A", "MRI Profile", "P1", "Define required documents for MRI", "MRI document checklist includes installation report, reception report, room plan, prevention plan, and conformity certificate if applicable.", "Section 5 - Person A", "To Do", "No", "No required-document rules by profile exist.", "Person A", "Sprint 3"],
  ["Person A", "KPI Dashboard", "P1", "Build KPI backend counts", "Backend returns counts by profile, status, maintenance, missing main equipment, and missing blocking documents.", "Section 5 - Person A", "To Do", "No", "Root redirects to installations index; no dashboard controller found.", "Person A", "Sprint 3"],
  ["Person A", "KPI Dashboard", "P1", "Create KPI dashboard view", "Dashboard shows clean cards/charts for active installations, profile split, operational rate, and exceptions.", "Section 5 - Person A", "To Do", "No", "No dashboard view exists.", "Person A", "Sprint 3"],
  ["Person A", "Tests", "P1", "Test biomedical can create and update MRI", "Feature test proves biomedical role can complete MRI parent/profile flow.", "Section 5 - Person A", "To Do", "No", "Only starter example tests exist.", "Person A", "Sprint 4"],
  ["Person A", "Tests", "P1", "Test manager cannot edit", "Manager role can view where enabled but cannot edit common or profile fields.", "Section 5 - Person A", "To Do", "No", "No manager role or authorization tests exist.", "Person A", "Sprint 4"],
  ["Person A", "Tests", "P1", "Test biomedical cannot archive", "Feature test rejects archive by biomedical and permits archive by admin.", "Section 5 - Person A", "To Do", "No", "Destroy route currently has no role guard.", "Person A", "Sprint 4"],
  ["Person A", "Tests", "P1", "Test invalid status transition is rejected", "Direct jump or forbidden transition is blocked and not historized as valid.", "Section 5 - Person A", "To Do", "No", "No transition service or tests exist.", "Person A", "Sprint 4"],
  ["Person A", "Tests", "P1", "Test dashboard numbers match seeded data", "Dashboard KPI values match seeded demo dataset.", "Section 5 - Person A", "To Do", "No", "No dashboard or seeded KPI test data exists.", "Person A", "Sprint 4"],

  ["Person B", "Cath Profile", "P0", "Clean up Cath migration and model", "Cath profile uses foreign key, one-to-one uniqueness, casts, and relationship consistency.", "Section 5 - Person B", "Partial", "Partial", "ProfilCatLab model/controller exist; migration lacks constrained FK/unique constraint.", "Person B", "Sprint 2"],
  ["Person B", "Cath Profile", "P0", "Add Cath validation rules", "Cath required fields block validation/operational status when missing.", "Section 5 - Person B", "To Do", "No", "Cath controller validates profile only in separate route; parent flow ignores it.", "Person B", "Sprint 2"],
  ["Person B", "Cath Profile", "P0", "Add Cath fields to create/edit flow", "Selecting CATHETERISME displays and saves Cath-specific fields in the parent installation form.", "Section 5 - Person B", "To Do", "No", "Installation create/edit forms do not include Cath fields.", "Person B", "Sprint 2"],
  ["Person B", "Cath Profile", "P0", "Display Cath tab on detail page", "Detail page shows Cath profile values when installation type is CATHETERISME.", "Section 5 - Person B", "Partial", "Partial", "Show page displays Cath details if profile exists, but parent create/update does not create it.", "Person B", "Sprint 2"],
  ["Person B", "Cath Profile", "P1", "Define required documents for Cath", "Cath checklist includes reception report, quality control, radioprotection documents, prevention plan, and technical reports.", "Section 5 - Person B", "To Do", "No", "No required-document rules by profile exist.", "Person B", "Sprint 3"],
  ["Person B", "Documents", "P0", "Add DMS-compatible document metadata fields", "Document table supports DMS id, profile type, file/url fallback, active flag, uploader, and replacement link.", "Section 5 - Person B", "Partial", "Partial", "Document table/controller exist but only category/version/status/blocking fields are stored.", "Person B", "Sprint 2"],
  ["Person B", "Documents", "P1", "Enforce active version rule for blocking docs", "Only one active version exists per blocking document category/profile/installation.", "Section 5 - Person B", "To Do", "No", "No active-version concept exists.", "Person B", "Sprint 3"],
  ["Person B", "Documents", "P0", "Improve document status display", "Detail page and document list show document category, version, status, blocking flag, and DMS/file reference.", "Section 5 - Person B", "Partial", "Partial", "Current display shows category/version/status/blocking but no DMS/file reference.", "Person B", "Sprint 2"],
  ["Person B", "Documents", "P1", "Add missing required-document detection", "List/detail/dashboard can flag installations missing required blocking documents.", "Section 5 - Person B", "To Do", "No", "No missing-document logic exists.", "Person B", "Sprint 3"],
  ["Person B", "Equipment Links", "P0", "Display main equipment by relation", "Detail/list show equipment code and designation, not only raw equipment_principal_id.", "Section 5 - Person B", "To Do", "No", "Detail page displays raw equipment principal ID.", "Person B", "Sprint 2"],
  ["Person B", "Equipment Links", "P0", "Constrain secondary equipment pivot", "Pivot has foreign keys, no duplicate installation/equipment pair, and clear role values.", "Section 5 - Person B", "Partial", "Partial", "Pivot table exists but lacks explicit constraints.", "Person B", "Sprint 2"],
  ["Person B", "Equipment Links", "P0", "Show sub-equipment through linked equipment", "Sub-equipment remains sourced from existing equipment relation and is visible from installation detail.", "Section 5 - Person B", "To Do", "No", "Installation detail lists linked equipment only, not sub-equipment through equipment.", "Person B", "Sprint 3"],
  ["Person B", "Calendar", "P1", "Add installation planning dates", "Installation stores planned and optional actual start/end dates for calendar tracking.", "Section 5 - Person B", "To Do", "No", "Installations table has no planning date fields.", "Person B", "Sprint 3"],
  ["Person B", "Calendar", "P1", "Build calendar controller query by month", "Calendar route returns installations planned for selected month with previous/next navigation.", "Section 5 - Person B", "To Do", "No", "No calendar route/controller found.", "Person B", "Sprint 3"],
  ["Person B", "Calendar", "P1", "Create monthly calendar or grouped-list view", "Corporate internal calendar view shows code, name, profile, status, client/site for selected month.", "Section 5 - Person B", "To Do", "No", "No calendar view exists.", "Person B", "Sprint 3"],
  ["Person B", "Tests", "P1", "Test Cath installation saves child profile", "Feature test proves parent create/update saves Cath child profile.", "Section 5 - Person B", "To Do", "No", "No Cath profile feature tests exist.", "Person B", "Sprint 4"],
  ["Person B", "Tests", "P1", "Test wrong profile child cannot be attached", "Cath data cannot attach to MRI installation and MRI data cannot attach to Cath installation.", "Section 5 - Person B", "To Do", "No", "No profile mismatch guard exists.", "Person B", "Sprint 4"],
  ["Person B", "Tests", "P1", "Test document attach/blocking/missing logic", "Tests cover document attachment, blocking flag, active version, and missing required document status.", "Section 5 - Person B", "To Do", "No", "No document workflow tests exist.", "Person B", "Sprint 4"],
  ["Person B", "Tests", "P1", "Test calendar selected month", "Calendar only shows installations planned in the requested month.", "Section 5 - Person B", "To Do", "No", "No calendar feature exists.", "Person B", "Sprint 4"],

  ["Optional", "Future Scope", "P2", "Manager validation workflow", "Manager validation remains disabled unless Philips confirms activation.", "Section 3 - P2 Backlog", "Deferred", "N/A", "PRD marks manager optional; do not block POC on this.", "Person A", "Optional"],
  ["Optional", "Future Scope", "P2", "Export CSV or Excel", "Filtered installation list can export if time allows.", "Section 3 - P2 Backlog", "Deferred", "N/A", "Should not outrank profile, roles, documents, dashboard, or calendar.", "Shared", "Optional"],
  ["Optional", "Budget and Time", "P2", "Add optional budget fields if confirmed", "budget_prevu, budget_reel, and devise are added only after stakeholder confirmation.", "Section 7 - Budget and Time", "Deferred", "N/A", "Current PRD/scope/tables do not require financial budget.", "Shared", "Optional"],
  ["Optional", "Budget and Time", "P2", "Add optional time/duration fields if confirmed", "temps_prevu_heures, temps_reel_heures, planned dates, and actual dates are added only as confirmed scope.", "Section 7 - Budget and Time", "Deferred", "N/A", "Calendar needs planning dates; full effort tracking is optional.", "Shared", "Optional"],
  ["Optional", "Documents", "P2", "Full DMS versioning integration", "Use real DMS versioning if structure is confirmed and quickly exploitable.", "Section 3 - P2 Backlog", "Deferred", "N/A", "POC table fallback is acceptable.", "Person B", "Optional"],
  ["Optional", "Audit", "P2", "Audit log beyond status/document events", "Critical field changes, validation, replacement, and archive events are logged in a dedicated audit table if time allows.", "Section 3 - P2 Backlog", "Deferred", "N/A", "Status history exists partially; full audit is future scope.", "Shared", "Optional"],
  ["Optional", "Interventions", "P2", "Integrate intervention table if found", "Open interventions can contribute to maintenance KPI if existing table is identified.", "Section 3 - P2 Backlog", "Deferred", "N/A", "PRD lists intervention table as an open question.", "Shared", "Optional"],
];

const headers = [
  "Owner",
  "Workstream",
  "Priority",
  "Task",
  "Acceptance Criteria",
  "Source",
  "Implementation Status",
  "Pushed to Git Repo?",
  "Current Repo Evidence / Notes",
  "Assigned To",
  "Target",
];

const palette = {
  navy: "#103A5D",
  blue: "#1F6FB2",
  cyan: "#A7D8F0",
  paleBlue: "#EAF4FB",
  slate: "#334155",
  gray: "#64748B",
  lightGray: "#F4F7FA",
  grid: "#D7E3EE",
  green: "#D9F2E3",
  greenText: "#0F7A3B",
  amber: "#FFF1CC",
  amberText: "#946200",
  red: "#FCE0E0",
  redText: "#A32020",
  purple: "#EDE7F6",
};

const workbook = Workbook.create();
const dashboard = workbook.worksheets.add("Dashboard");
const checklist = workbook.worksheets.add("Checklist");
const permissions = workbook.worksheets.add("Permissions");
const gitSheet = workbook.worksheets.add("Git Snapshot");
const lists = workbook.worksheets.add("Lists");

for (const sheet of [dashboard, checklist, permissions, gitSheet, lists]) {
  sheet.showGridLines = false;
}

function setTitle(sheet, range, title, subtitle) {
  sheet.getRange(range).merge();
  const startCell = range.split(":")[0];
  sheet.getRange(startCell).values = [[title]];
  sheet.getRange(startCell).format = {
    fill: palette.navy,
    font: { color: "#FFFFFF", bold: true, size: 18 },
    horizontalAlignment: "center",
    verticalAlignment: "center",
  };
  const row = Number(startCell.match(/\d+/)[0]);
  const col = startCell.match(/[A-Z]+/)[0];
  const subtitleCell = `${col}${row + 1}`;
  sheet.getRange(`${subtitleCell}:K${row + 1}`).merge();
  sheet.getRange(subtitleCell).values = [[subtitle]];
  sheet.getRange(subtitleCell).format = {
    fill: palette.paleBlue,
    font: { color: palette.slate, italic: true, size: 10 },
    horizontalAlignment: "center",
  };
}

function formatCard(sheet, range, labelCell, valueCell, fill) {
  sheet.getRange(range).format = {
    fill,
    borders: { preset: "outside", style: "thin", color: palette.grid },
  };
  sheet.getRange(labelCell).format = {
    font: { color: palette.gray, bold: true, size: 9 },
    horizontalAlignment: "center",
  };
  sheet.getRange(valueCell).format = {
    font: { color: palette.navy, bold: true, size: 18 },
    horizontalAlignment: "center",
    verticalAlignment: "center",
  };
}

// Lists
lists.getRange("A1:D1").values = [["Implementation Status", "Pushed Status", "Priority", "Owner"]];
lists.getRange("A2:A8").values = [["To Do"], ["In Progress"], ["Partial"], ["Blocked"], ["Done"], ["Deferred"], ["Not Applicable"]];
lists.getRange("B2:B5").values = [["No"], ["Partial"], ["Yes"], ["N/A"]];
lists.getRange("C2:C4").values = [["P0"], ["P1"], ["P2"]];
lists.getRange("D2:D6").values = [["Shared"], ["Person A"], ["Person B"], ["Optional"], ["Both"]];
lists.getRange("A1:D1").format = { fill: palette.navy, font: { color: "#FFFFFF", bold: true } };
lists.getRange("A1:D8").format.borders = { preset: "all", style: "thin", color: palette.grid };
lists.getRange("A:D").format.columnWidthPx = 170;

// Checklist
setTitle(
  checklist,
  "A1:K1",
  "Installation POC Delivery Checklist",
  "Corporate task tracker for Person A, Person B, shared foundation, git push status, and demo readiness."
);
checklist.getRange("A4:K4").values = [headers];
checklist.getRangeByIndexes(4, 0, tasks.length, headers.length).values = tasks;
const tableEndRow = 4 + tasks.length;
const checklistRange = `A4:K${tableEndRow}`;
const table = checklist.tables.add(checklistRange, true, "InstallationPOCChecklist");
table.style = "TableStyleMedium2";
table.showFilterButton = true;
table.showBandedRows = true;
checklist.freezePanes.freezeRows(4);
checklist.freezePanes.freezeColumns(3);

checklist.getRange("A4:K4").format = {
  fill: palette.blue,
  font: { color: "#FFFFFF", bold: true, size: 10 },
  horizontalAlignment: "center",
  verticalAlignment: "center",
  wrapText: true,
};
checklist.getRange(`A5:K${tableEndRow}`).format = {
  fill: "#FFFFFF",
  font: { color: palette.slate, size: 9 },
  verticalAlignment: "top",
  wrapText: true,
  borders: { preset: "all", style: "thin", color: palette.grid },
};
checklist.getRange("A:A").format.columnWidthPx = 90;
checklist.getRange("B:B").format.columnWidthPx = 150;
checklist.getRange("C:C").format.columnWidthPx = 70;
checklist.getRange("D:D").format.columnWidthPx = 290;
checklist.getRange("E:E").format.columnWidthPx = 380;
checklist.getRange("F:F").format.columnWidthPx = 190;
checklist.getRange("G:G").format.columnWidthPx = 145;
checklist.getRange("H:H").format.columnWidthPx = 145;
checklist.getRange("I:I").format.columnWidthPx = 380;
checklist.getRange("J:J").format.columnWidthPx = 110;
checklist.getRange("K:K").format.columnWidthPx = 90;
checklist.getRange(`A5:K${tableEndRow}`).format.rowHeightPx = 55;

checklist.getRange(`G5:G${tableEndRow}`).dataValidation = {
  rule: { type: "list", values: ["To Do", "In Progress", "Partial", "Blocked", "Done", "Deferred", "Not Applicable"] },
};
checklist.getRange(`H5:H${tableEndRow}`).dataValidation = {
  rule: { type: "list", values: ["No", "Partial", "Yes", "N/A"] },
};
checklist.getRange(`C5:C${tableEndRow}`).dataValidation = {
  rule: { type: "list", values: ["P0", "P1", "P2"] },
};
checklist.getRange(`A5:A${tableEndRow}`).dataValidation = {
  rule: { type: "list", values: ["Shared", "Person A", "Person B", "Optional"] },
};

for (const [range, text, fill, fontColor] of [
  [`C5:C${tableEndRow}`, "P0", palette.red, palette.redText],
  [`C5:C${tableEndRow}`, "P1", palette.amber, palette.amberText],
  [`C5:C${tableEndRow}`, "P2", palette.purple, palette.navy],
  [`G5:G${tableEndRow}`, "Done", palette.green, palette.greenText],
  [`G5:G${tableEndRow}`, "Deferred", palette.lightGray, palette.gray],
  [`G5:G${tableEndRow}`, "Blocked", palette.red, palette.redText],
  [`H5:H${tableEndRow}`, "Yes", palette.green, palette.greenText],
  [`H5:H${tableEndRow}`, "No", palette.red, palette.redText],
  [`H5:H${tableEndRow}`, "Partial", palette.amber, palette.amberText],
  [`H5:H${tableEndRow}`, "N/A", palette.lightGray, palette.gray],
]) {
  checklist.getRange(range).conditionalFormats.add("containsText", {
    text,
    format: { fill, font: { color: fontColor, bold: true } },
  });
}

// Dashboard
setTitle(
  dashboard,
  "A1:K1",
  "Installation POC Checklist Dashboard",
  `Repo snapshot: ${repoSnapshot.branch} -> ${repoSnapshot.upstream}; pushed status is based on local git state checked ${repoSnapshot.checkedAt}.`
);

dashboard.getRange("A4:K4").merge();
dashboard.getRange("A4").values = [["Executive Summary"]];
dashboard.getRange("A4").format = {
  fill: palette.blue,
  font: { color: "#FFFFFF", bold: true, size: 12 },
};

const lastDataRow = tableEndRow;
const formulas = [
  ["A6:B8", "A6", "B7", "Total Tasks", `=COUNTA(Checklist!$D$5:$D$${lastDataRow})`, palette.paleBlue],
  ["D6:E8", "D6", "E7", "P0 Must Fix", `=COUNTIF(Checklist!$C$5:$C$${lastDataRow},"P0")`, palette.red],
  ["G6:H8", "G6", "H7", "Person A Tasks", `=COUNTIF(Checklist!$A$5:$A$${lastDataRow},"Person A")`, palette.cyan],
  ["J6:K8", "J6", "K7", "Person B Tasks", `=COUNTIF(Checklist!$A$5:$A$${lastDataRow},"Person B")`, palette.cyan],
  ["A10:B12", "A10", "B11", "Pushed Yes", `=COUNTIF(Checklist!$H$5:$H$${lastDataRow},"Yes")`, palette.green],
  ["D10:E12", "D10", "E11", "Pushed Partial", `=COUNTIF(Checklist!$H$5:$H$${lastDataRow},"Partial")`, palette.amber],
  ["G10:H12", "G10", "H11", "Not Pushed", `=COUNTIF(Checklist!$H$5:$H$${lastDataRow},"No")`, palette.red],
  ["J10:K12", "J10", "K11", "Deferred/N-A", `=COUNTIF(Checklist!$H$5:$H$${lastDataRow},"N/A")`, palette.lightGray],
];
for (const [range, labelCell, valueCell, label, formula, fill] of formulas) {
  dashboard.getRange(labelCell).values = [[label]];
  dashboard.getRange(valueCell).formulas = [[formula]];
  formatCard(dashboard, range, labelCell, valueCell, fill);
}

dashboard.getRange("A15:D15").values = [["Owner", "P0", "P1", "P2"]];
dashboard.getRange("A16:D19").values = [
  ["Shared", null, null, null],
  ["Person A", null, null, null],
  ["Person B", null, null, null],
  ["Optional", null, null, null],
];
dashboard.getRange("B16").formulas = [[`=COUNTIFS(Checklist!$A$5:$A$${lastDataRow},$A16,Checklist!$C$5:$C$${lastDataRow},B$15)`]];
dashboard.getRange("B16:D19").fillDown();
dashboard.getRange("B16:D19").fillRight();
dashboard.getRange("A15:D19").format = {
  borders: { preset: "all", style: "thin", color: palette.grid },
};
dashboard.getRange("A15:D15").format = {
  fill: palette.navy,
  font: { color: "#FFFFFF", bold: true },
  horizontalAlignment: "center",
};
dashboard.getRange("A16:A19").format = { fill: palette.paleBlue, font: { bold: true, color: palette.navy } };
dashboard.getRange("B16:D19").format = { horizontalAlignment: "center", font: { color: palette.slate } };

dashboard.getRange("F15:H15").values = [["Pushed Status", "Count", "Share"]];
dashboard.getRange("F16:H19").values = [["No", null, null], ["Partial", null, null], ["Yes", null, null], ["N/A", null, null]];
dashboard.getRange("G16").formulas = [[`=COUNTIF(Checklist!$H$5:$H$${lastDataRow},F16)`]];
dashboard.getRange("G16:G19").fillDown();
dashboard.getRange("H16").formulas = [[`=IFERROR(G16/SUM($G$16:$G$19),0)`]];
dashboard.getRange("H16:H19").fillDown();
dashboard.getRange("H16:H19").format.numberFormat = "0%";
dashboard.getRange("F15:H19").format = { borders: { preset: "all", style: "thin", color: palette.grid } };
dashboard.getRange("F15:H15").format = { fill: palette.navy, font: { color: "#FFFFFF", bold: true } };

const ownerChart = dashboard.charts.add("bar", dashboard.getRange("A15:D19"));
ownerChart.title = "Task Load by Owner and Priority";
ownerChart.hasLegend = true;
ownerChart.setPosition("A22", "E38");
const pushedChart = dashboard.charts.add("doughnut", dashboard.getRange("F15:G19"));
pushedChart.title = "Git Push Status";
pushedChart.hasLegend = true;
pushedChart.setPosition("G22", "K38");

dashboard.getRange("A41:K41").merge();
dashboard.getRange("A41").values = [["Decision Notes"]];
dashboard.getRange("A41").format = { fill: palette.blue, font: { color: "#FFFFFF", bold: true } };
dashboard.getRange("A42:K45").values = [
  ["1", "Role management and proper MRI/Cath separation are P0 and should be implemented before demo polish.", "", "", "", "", "", "", "", "", ""],
  ["2", "KPI dashboard and monthly calendar are P1 because the latest request makes them expected demo items.", "", "", "", "", "", "", "", "", ""],
  ["3", "Budget and full time-spent tracking remain P2 until Philips confirms they are required.", "", "", "", "", "", "", "", "", ""],
  ["4", "The pushed-status column is editable; current values are based on the local git snapshot, not remote CI.", "", "", "", "", "", "", "", "", ""],
];
dashboard.getRange("A42:A45").format = { fill: palette.paleBlue, font: { bold: true, color: palette.navy }, horizontalAlignment: "center" };
dashboard.getRange("B42:K45").merge(true);
dashboard.getRange("A42:K45").format = {
  fill: "#FFFFFF",
  borders: { preset: "all", style: "thin", color: palette.grid },
  wrapText: true,
};
dashboard.getRange("A:K").format.columnWidthPx = 95;
dashboard.getRange("B:B").format.columnWidthPx = 125;
dashboard.getRange("E:E").format.columnWidthPx = 125;
dashboard.getRange("H:H").format.columnWidthPx = 125;

// Permissions
setTitle(
  permissions,
  "A1:E1",
  "POC Permission Matrix",
  "Minimal internal roles from the PRD and backlog. Manager remains optional/read-only by default."
);
const permissionRows = [
  ["Action", "Admin", "Biomedical", "Manager Optional", "Implementation Note"],
  ["View list/detail", "Yes", "Yes", "Yes", "All internal users can view if authenticated."],
  ["View KPIs/calendar", "Yes", "Yes", "Yes if enabled", "Manager only if Philips activates the role."],
  ["Create installation", "Yes", "Yes", "No", "Person A owns policy/middleware."],
  ["Edit common fields", "Yes", "Yes", "No", "Guard in update action and UI."],
  ["Edit profile fields", "Yes", "Yes", "No", "Applies to MRI and Cath child profiles."],
  ["Link equipment", "Yes", "Yes", "No", "Person B owns relation display and constraints."],
  ["Attach/replace document", "Yes", "Yes", "No", "Person B owns DMS fallback metadata."],
  ["Operational status changes", "Yes", "Yes", "Validation only if enabled", "Transition service owned by Person A."],
  ["Archive installation", "Yes", "No", "No", "Admin-only."],
  ["Manage users/roles", "Yes", "No", "No", "Keep simple for POC."],
];
permissions.getRangeByIndexes(3, 0, permissionRows.length, permissionRows[0].length).values = permissionRows;
permissions.tables.add(`A4:E${3 + permissionRows.length}`, true, "PermissionMatrix").style = "TableStyleMedium4";
permissions.getRange("A4:E4").format = { fill: palette.blue, font: { color: "#FFFFFF", bold: true } };
permissions.getRange(`A5:E${3 + permissionRows.length}`).format = {
  wrapText: true,
  borders: { preset: "all", style: "thin", color: palette.grid },
};
permissions.getRange("A:A").format.columnWidthPx = 210;
permissions.getRange("B:D").format.columnWidthPx = 125;
permissions.getRange("E:E").format.columnWidthPx = 330;
permissions.freezePanes.freezeRows(4);

// Git Snapshot
setTitle(
  gitSheet,
  "A1:F1",
  "Git Snapshot for Checklist Push Column",
  "Used to initialize the 'Pushed to Git Repo?' field. Update this sheet after each push."
);
gitSheet.getRange("A4:B9").values = [
  ["Checked At", repoSnapshot.checkedAt],
  ["Current Branch", repoSnapshot.branch],
  ["Upstream", repoSnapshot.upstream],
  ["Remote", repoSnapshot.remote],
  ["Status Summary", repoSnapshot.status],
  ["Interpretation", "Rows marked Partial have baseline code present, but the requested checklist change is not fully pushed/completed."],
];
gitSheet.getRange("A4:A9").format = { fill: palette.paleBlue, font: { bold: true, color: palette.navy } };
gitSheet.getRange("B4:B9").format = { fill: "#FFFFFF", wrapText: true, font: { color: palette.slate } };
gitSheet.getRange("A4:B9").format.borders = { preset: "all", style: "thin", color: palette.grid };
gitSheet.getRange("A:A").format.columnWidthPx = 180;
gitSheet.getRange("B:B").format.columnWidthPx = 650;

// Final workbook formatting
for (const sheet of [dashboard, checklist, permissions, gitSheet]) {
  sheet.getRange("1:1").format.rowHeightPx = 34;
  sheet.getRange("2:2").format.rowHeightPx = 28;
}

// Compact inspections and visual renders for QA.
const dashboardInspect = await workbook.inspect({
  kind: "table",
  range: "Dashboard!A1:K19",
  include: "values,formulas",
  tableMaxRows: 25,
  tableMaxCols: 11,
});
console.log(dashboardInspect.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 200 },
  summary: "final formula error scan",
});
console.log(errors.ndjson);

for (const sheetName of ["Dashboard", "Checklist", "Permissions", "Git Snapshot"]) {
  const preview = await workbook.render({ sheetName, autoCrop: "all", scale: 1, format: "png" });
  await fs.writeFile(`${outputDir}/${sheetName.replaceAll(" ", "_").toLowerCase()}_preview.png`, new Uint8Array(await preview.arrayBuffer()));
}

const xlsx = await SpreadsheetFile.exportXlsx(workbook);
await xlsx.save(`${outputDir}/installation_poc_person_a_b_checklist.xlsx`);
console.log(`${outputDir}/installation_poc_person_a_b_checklist.xlsx`);
