import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "outputs/installation_poc_checklist";
await fs.mkdir(outputDir, { recursive: true });

const repo = {
  branch: "main",
  upstream: "origin/main",
  remote: "https://github.com/ghaythmabrouk240-oss/extention_instalation.git",
  checkedAt: "2026-06-09",
  pushedNote: "No checklist implementation changes are confirmed as pushed. Local workspace still has uncommitted files.",
};

const rows = [
  ["Shared", "P0", "Foundation", "Finalize naming and constants", "Common names for profile types, statuses, roles, priorities, document states, and planning dates are agreed and reused.", "To Do", "No", "Create shared constants/config before feature work continues."],
  ["Shared", "P0", "Schema", "Clean migrations and constraints", "Migrations use consistent foreign keys, one-to-one profile constraints, and no orphan rows for profiles/documents/status/equipment links.", "Partial", "No", "Some tables exist, but Cath/documents/status/pivot constraints need cleanup."],
  ["Shared", "P0", "Permissions", "Define roles and status matrix", "Admin, biomedical, and optional manager permissions are written and mapped to allowed status transitions.", "To Do", "No", "Use the PRD matrix; keep manager read-only/disabled unless confirmed."],
  ["Shared", "P0", "Demo Data", "Fix seed data", "Seed creates valid demo users, clients, equipment, one MRI installation, one Cath installation, documents, and status history.", "To Do", "No", "Current DummyDataSeeder is not reliable against the equipment schema."],
  ["Shared", "P0", "Installation List", "Add simple filters", "List supports basic filtering by profile, status, client/site, main equipment, and missing documents when available.", "To Do", "No", "Index currently loads all installations without filters."],
  ["Shared", "P0", "Integration", "Validate vertical slice", "Fresh migration/seed works and both MRI and Cath installations can be created, viewed, edited, and historized.", "To Do", "No", "Run after schema/profile/seed cleanup."],
  ["Shared", "P0", "Documentation", "Prepare demo script and limitations note", "Demo script shows MRI, Cath, equipment, document, status, dashboard, calendar, and blocked unauthorized action.", "Partial", "No", "Planning document exists locally, but implementation/demo artifacts are not pushed."],

  ["Person A", "P0", "Roles", "Implement role storage and authorization", "Users have simple roles and server-side policy/middleware guards protect module actions.", "To Do", "No", "Add user role field and InstallationPolicy or equivalent middleware."],
  ["Person A", "P0", "Status Workflow", "Implement transition service", "Invalid status jumps are rejected, allowed transitions are historized, and archive is admin-only.", "To Do", "No", "Current edit form allows broad status selection."],
  ["Person A", "P0", "MRI Profile", "Clean MRI model and schema", "MRI profile has relationship, casts, validation-ready fields, and one-to-one integrity with Installation.", "Partial", "No", "MRI model/table exist but are not fully integrated."],
  ["Person A", "P0", "MRI Profile", "Integrate MRI in create/edit/show", "Selecting IRM displays/saves MRI-specific fields and detail page shows MRI profile values.", "To Do", "No", "Installation forms currently save common fields only; show page loads Cath profile only."],
  ["Person A", "P1", "MRI Documents", "Define MRI required documents", "MRI checklist tracks required installation/reception/room/prevention/conformity documents.", "To Do", "No", "Needed for missing-document KPI and operational readiness."],
  ["Person A", "P1", "Dashboard", "Build KPI dashboard", "Dashboard shows profile split, status counts, operational rate, maintenance count, missing equipment, missing blocking docs, and planned-this-month count.", "To Do", "No", "Root route currently redirects to installation list."],
  ["Person A", "P1", "Tests", "Add Person A feature tests", "Tests cover biomedical MRI create/update, manager cannot edit, biomedical cannot archive, invalid transition, and KPI counts.", "To Do", "No", "Only starter tests are present."],

  ["Person B", "P0", "Cath Profile", "Clean Cath model and schema", "Cath profile has relationship, casts, constrained foreign key, and one-to-one integrity with Installation.", "Partial", "No", "Cath controller/model exist, but migration lacks strong constraints."],
  ["Person B", "P0", "Cath Profile", "Integrate Cath in create/edit/show", "Selecting CATHETERISME displays/saves Cath-specific fields and detail page shows Cath profile values.", "Partial", "No", "Show page can display Cath if present; parent flow does not create/update it."],
  ["Person B", "P1", "Cath Documents", "Define Cath required documents", "Cath checklist tracks reception, quality control, radioprotection, prevention, and technical report documents.", "To Do", "No", "Needed for missing-document logic."],
  ["Person B", "P0", "Documents", "Implement document attachment POC", "Document table supports category, version, status, blocking flag, DMS object id or file/url fallback, active flag, uploader, and replacement link.", "Partial", "No", "Current table stores only basic metadata."],
  ["Person B", "P1", "Documents", "Add active version and missing-doc rules", "Only one active blocking version per type is allowed and missing required documents are visible.", "To Do", "No", "No active-version or missing-document logic exists."],
  ["Person B", "P0", "Equipment", "Improve equipment and sub-equipment display", "Main equipment is shown by relation, secondary equipment pivot is constrained, and sub-equipment is visible through linked equipment.", "Partial", "No", "Main equipment currently appears mostly as raw ID; pivot lacks constraints."],
  ["Person B", "P1", "Calendar", "Build monthly installation calendar", "Installation planning dates exist and a monthly view shows code, name, profile, status, and client/site.", "To Do", "No", "No installation-level planning dates or calendar view exist."],
  ["Person B", "P1", "Tests", "Add Person B feature tests", "Tests cover Cath child save, wrong-profile guard, document attach/blocking/missing logic, and calendar month filtering.", "To Do", "No", "No feature tests for these flows exist."],

  ["Optional", "P2", "Manager", "Manager validation workflow", "Manager approval remains disabled unless Philips confirms activation.", "Deferred", "N/A", "Do not block POC delivery."],
  ["Optional", "P2", "Export", "Export filtered list", "CSV/Excel export is added only after P0/P1 demo scope is stable.", "Deferred", "N/A", "Could be useful, but not central."],
  ["Optional", "P2", "Budget", "Budget fields if confirmed", "Add budget_prevu, budget_reel, and devise only if stakeholders request financial tracking.", "Deferred", "N/A", "PRD and scope do not make budget mandatory."],
  ["Optional", "P2", "Time Tracking", "Time and effort fields if confirmed", "Add planned/actual effort fields only if stakeholders request installation duration tracking.", "Deferred", "N/A", "Calendar needs dates; full effort tracking is optional."],
  ["Optional", "P2", "DMS/Audit", "Full DMS, audit, and intervention integration", "Real DMS versioning, audit logs, and intervention linkage are added only if existing tables are confirmed.", "Deferred", "N/A", "Use POC fallback first."],
];

const headers = ["Owner", "Priority", "Workstream", "Task", "Deliverable / Done When", "Status", "Pushed?", "Next Step / Evidence"];

const palette = {
  navy: "#123B5D",
  blue: "#236FAF",
  lightBlue: "#EAF4FB",
  midBlue: "#CFE8F6",
  text: "#1F2937",
  muted: "#64748B",
  grid: "#D7E3EE",
  green: "#DDF4E8",
  greenText: "#106B3A",
  amber: "#FFF1CC",
  amberText: "#8A5A00",
  red: "#F8DADA",
  redText: "#9B1C1C",
  purple: "#ECE7F6",
  gray: "#F3F6F8",
};

const workbook = Workbook.create();
const sheet = workbook.worksheets.add("Checklist");
const ref = workbook.worksheets.add("Reference");
sheet.showGridLines = false;
ref.showGridLines = false;

function mergeTitle(ws, endCol, title, subtitle) {
  ws.getRange(`A1:${endCol}1`).merge();
  ws.getRange("A1").values = [[title]];
  ws.getRange("A1").format = {
    fill: palette.navy,
    font: { color: "#FFFFFF", bold: true, size: 17 },
    horizontalAlignment: "center",
    verticalAlignment: "center",
  };
  ws.getRange(`A2:${endCol}2`).merge();
  ws.getRange("A2").values = [[subtitle]];
  ws.getRange("A2").format = {
    fill: palette.lightBlue,
    font: { color: palette.text, italic: true, size: 10 },
    horizontalAlignment: "center",
  };
}

mergeTitle(
  sheet,
  "H",
  "Installation POC - Person A / Person B Checklist",
  "Simplified delivery tracker with clear task ownership, current status, and pushed-to-git visibility."
);

// Summary cards.
const cardData = [
  ["Total Tasks", `=COUNTA($D$11:$D$${10 + rows.length})`],
  ["P0 Must Fix", `=COUNTIF($B$11:$B$${10 + rows.length},"P0")`],
  ["Person A", `=COUNTIF($A$11:$A$${10 + rows.length},"Person A")`],
  ["Person B", `=COUNTIF($A$11:$A$${10 + rows.length},"Person B")`],
  ["Not Pushed", `=COUNTIF($G$11:$G$${10 + rows.length},"No")`],
];
for (let i = 0; i < cardData.length; i += 1) {
  const col = String.fromCharCode("A".charCodeAt(0) + i);
  sheet.getRange(`${col}4`).values = [[cardData[i][0]]];
  sheet.getRange(`${col}5`).formulas = [[cardData[i][1]]];
  sheet.getRange(`${col}4:${col}6`).format = {
    fill: i === 1 || i === 4 ? palette.red : palette.midBlue,
    borders: { preset: "outside", style: "thin", color: palette.grid },
  };
  sheet.getRange(`${col}4`).format = {
    font: { color: palette.muted, bold: true, size: 9 },
    horizontalAlignment: "center",
  };
  sheet.getRange(`${col}5`).format = {
    font: { color: palette.navy, bold: true, size: 16 },
    horizontalAlignment: "center",
  };
}

sheet.getRange("A8:H8").merge();
sheet.getRange("A8").values = [[`Git note: ${repo.branch} -> ${repo.upstream}. ${repo.pushedNote}`]];
sheet.getRange("A8").format = {
  fill: palette.amber,
  font: { color: palette.amberText, bold: true },
  wrapText: true,
};

// Main table.
sheet.getRange("A10:H10").values = [headers];
sheet.getRangeByIndexes(10, 0, rows.length, headers.length).values = rows;
const lastRow = 10 + rows.length;
const table = sheet.tables.add(`A10:H${lastRow}`, true, "SimplifiedPOCChecklist");
table.style = "TableStyleMedium2";
table.showFilterButton = true;
table.showBandedRows = true;

sheet.getRange("A10:H10").format = {
  fill: palette.blue,
  font: { color: "#FFFFFF", bold: true, size: 10 },
  horizontalAlignment: "center",
  wrapText: true,
};
sheet.getRange(`A11:H${lastRow}`).format = {
  font: { color: palette.text, size: 9 },
  wrapText: true,
  verticalAlignment: "top",
  borders: { preset: "all", style: "thin", color: palette.grid },
};
sheet.freezePanes.freezeRows(10);
sheet.freezePanes.freezeColumns(4);

sheet.getRange("A:A").format.columnWidthPx = 100;
sheet.getRange("B:B").format.columnWidthPx = 72;
sheet.getRange("C:C").format.columnWidthPx = 130;
sheet.getRange("D:D").format.columnWidthPx = 240;
sheet.getRange("E:E").format.columnWidthPx = 430;
sheet.getRange("F:F").format.columnWidthPx = 115;
sheet.getRange("G:G").format.columnWidthPx = 92;
sheet.getRange("H:H").format.columnWidthPx = 360;
sheet.getRange(`A11:H${lastRow}`).format.rowHeightPx = 48;

sheet.getRange(`B11:B${lastRow}`).dataValidation = { rule: { type: "list", values: ["P0", "P1", "P2"] } };
sheet.getRange(`F11:F${lastRow}`).dataValidation = { rule: { type: "list", values: ["To Do", "In Progress", "Partial", "Blocked", "Done", "Deferred"] } };
sheet.getRange(`G11:G${lastRow}`).dataValidation = { rule: { type: "list", values: ["No", "Yes", "N/A"] } };

const cf = [
  [`B11:B${lastRow}`, "P0", palette.red, palette.redText],
  [`B11:B${lastRow}`, "P1", palette.amber, palette.amberText],
  [`B11:B${lastRow}`, "P2", palette.purple, palette.navy],
  [`F11:F${lastRow}`, "Done", palette.green, palette.greenText],
  [`F11:F${lastRow}`, "Partial", palette.amber, palette.amberText],
  [`F11:F${lastRow}`, "Deferred", palette.gray, palette.muted],
  [`F11:F${lastRow}`, "Blocked", palette.red, palette.redText],
  [`G11:G${lastRow}`, "Yes", palette.green, palette.greenText],
  [`G11:G${lastRow}`, "No", palette.red, palette.redText],
  [`G11:G${lastRow}`, "N/A", palette.gray, palette.muted],
];
for (const [range, text, fill, color] of cf) {
  sheet.getRange(range).conditionalFormats.add("containsText", {
    text,
    format: { fill, font: { color, bold: true } },
  });
}

// Reference sheet: concise permission matrix and interpretation notes.
mergeTitle(
  ref,
  "F",
  "Reference - Permissions and Git Interpretation",
  "Short reference used by the simplified checklist."
);

const permissions = [
  ["Action", "Admin", "Biomedical", "Manager Optional", "Owner", "Note"],
  ["View list/detail", "Yes", "Yes", "Yes", "Shared", "Internal only."],
  ["Create/edit installation", "Yes", "Yes", "No", "Person A", "Protected by policy/middleware."],
  ["Edit MRI profile", "Yes", "Yes", "No", "Person A", "Profile-specific ownership."],
  ["Edit Cath profile", "Yes", "Yes", "No", "Person B", "Profile-specific ownership."],
  ["Attach documents", "Yes", "Yes", "No", "Person B", "DMS fallback accepted for POC."],
  ["Change operational status", "Yes", "Yes", "Validation only if enabled", "Person A", "Transition service required."],
  ["Archive", "Yes", "No", "No", "Person A", "Admin-only."],
  ["View KPIs/calendar", "Yes", "Yes", "Yes if enabled", "Shared", "Dashboard and calendar are expected demo items."],
];
ref.getRangeByIndexes(4, 0, permissions.length, permissions[0].length).values = permissions;
ref.tables.add(`A5:F${4 + permissions.length}`, true, "SimplePermissionMatrix").style = "TableStyleMedium4";
ref.getRange("A5:F5").format = { fill: palette.blue, font: { color: "#FFFFFF", bold: true } };
ref.getRange(`A6:F${4 + permissions.length}`).format = {
  wrapText: true,
  borders: { preset: "all", style: "thin", color: palette.grid },
};

const noteStart = 16;
ref.getRange(`A${noteStart}:F${noteStart}`).merge();
ref.getRange(`A${noteStart}`).values = [["Git Interpretation"]];
ref.getRange(`A${noteStart}`).format = { fill: palette.blue, font: { color: "#FFFFFF", bold: true } };
ref.getRange(`A${noteStart + 1}:F${noteStart + 5}`).values = [
  ["Checked At", repo.checkedAt, "", "", "", ""],
  ["Branch", repo.branch, "", "", "", ""],
  ["Upstream", repo.upstream, "", "", "", ""],
  ["Remote", repo.remote, "", "", "", ""],
  ["Pushed?", "No = not confirmed pushed; Yes = pushed to remote; N/A = optional/deferred or not a code change.", "", "", "", ""],
];
ref.getRange(`A${noteStart + 1}:A${noteStart + 5}`).format = { fill: palette.lightBlue, font: { bold: true, color: palette.navy } };
ref.getRange(`B${noteStart + 1}:F${noteStart + 5}`).merge(true);
ref.getRange(`A${noteStart + 1}:F${noteStart + 5}`).format = {
  borders: { preset: "all", style: "thin", color: palette.grid },
  wrapText: true,
};

ref.getRange("A:A").format.columnWidthPx = 200;
ref.getRange("B:D").format.columnWidthPx = 120;
ref.getRange("E:E").format.columnWidthPx = 100;
ref.getRange("F:F").format.columnWidthPx = 310;

for (const ws of [sheet, ref]) {
  ws.getRange("1:1").format.rowHeightPx = 34;
  ws.getRange("2:2").format.rowHeightPx = 28;
}

const inspect = await workbook.inspect({
  kind: "table",
  range: "Checklist!A1:H20",
  include: "values,formulas",
  tableMaxRows: 20,
  tableMaxCols: 8,
});
console.log(inspect.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 100 },
  summary: "final formula error scan",
});
console.log(errors.ndjson);

for (const sheetName of ["Checklist", "Reference"]) {
  const preview = await workbook.render({ sheetName, autoCrop: "all", scale: 1, format: "png" });
  await fs.writeFile(`${outputDir}/${sheetName.toLowerCase()}_simple_preview.png`, new Uint8Array(await preview.arrayBuffer()));
}

const xlsx = await SpreadsheetFile.exportXlsx(workbook);
await xlsx.save(`${outputDir}/installation_poc_person_a_b_checklist_simple.xlsx`);
console.log(`${outputDir}/installation_poc_person_a_b_checklist_simple.xlsx`);
