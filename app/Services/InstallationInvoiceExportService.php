<?php

namespace App\Services;

use App\Models\Installation;
use App\Models\InstallationBudget;
use App\Models\InstallationExpense;
use App\Models\InstallationTimePenalty;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InstallationInvoiceExportService
{
    public function exportInvoice(Installation $installation): string
    {
        $installation->loadMissing('client', 'equipementPrincipal');

        $spreadsheet = new Spreadsheet();

        // Create sheets in reverse order so Synthese is first
        $this->createWarrantyContractSheet($spreadsheet, $installation);
        $this->createTimePenaltySheet($spreadsheet, $installation);
        $this->createExpensesSheet($spreadsheet, $installation);
        $this->createSummarySheet($spreadsheet, $installation);
        
        // Set Synthese as active sheet
        $spreadsheet->setActiveSheetIndex(3);

        $exportDirectory = storage_path('app/exports');
        if (! is_dir($exportDirectory)) {
            mkdir($exportDirectory, 0755, true);
        }

        $fileName = 'facture_installation_' . $installation->code_installation . '_' . date('Y-m-d-His') . '.xlsx';
        $filePath = $exportDirectory . DIRECTORY_SEPARATOR . $fileName;

        (new Xlsx($spreadsheet))->save($filePath);

        return $filePath;
    }

    protected function createSummarySheet(Spreadsheet $spreadsheet, Installation $installation): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Synthese');

        $budgetService = app(InstallationBudgetService::class);
        $summary = $budgetService->getBudgetSummary($installation->id);
        $budget = InstallationBudget::where('installation_id', $installation->id)->first();
        
        // Ensure budget exists with default values
        if (!$budget) {
            $budget = InstallationBudget::create([
                'installation_id' => $installation->id,
                'regime_prise_en_charge' => 'garantie',
                'devise' => 'EUR',
                'budget_prevu' => 0,
                'total_frais' => 0,
                'total_penalites' => 0,
                'total_final' => 0,
                'statut_validation' => 'brouillon',
            ]);
        }
        
        // Always recalculate totals to ensure accurate values
        $budgetService->updateBudgetTotals($installation->id);
        $budget->refresh();
        
        // Get fresh summary after recalculation
        $summary = $budgetService->getBudgetSummary($installation->id);
        
        $currency = $budget->devise ?? 'EUR';

        $sheet->setCellValue('A1', 'BUDGET INSTALLATION - ' . $installation->code_installation);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:D1');

        $identityRows = [
            ['Nom', $installation->nom],
            ['Profil', $installation->type_profil],
            ['Client', $installation->client?->nom ?? 'N/A'],
            ['Statut installation', $installation->statut],
            ['Equipement principal', $installation->equipementPrincipal?->code ?? 'N/A'],
            ['Date export', date('d/m/Y')],
        ];

        $row = 3;
        foreach ($identityRows as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label . ':');
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'COUVERTURE');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $coverageRows = [
            ['Regime de prise en charge', $this->getRegimeLabel($summary['regime_prise_en_charge'])],
            ['Statut couverture', $this->getCoverageLabel($summary['statut_couverture'])],
            ['Reference contrat', $summary['reference_contrat'] ?? 'N/A'],
        ];

        foreach ($coverageRows as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label . ':');
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'RESUME BUDGETAIRE');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        // Use actual calculated values from service
        $budgetRows = [
            ['Budget prevu', $budget->budget_prevu ?? 0],
            ['Total frais', $summary['total_frais'] ?? 0],
            ['Total penalites', $summary['total_penalites'] ?? 0],
            ['Total final', $summary['total_final'] ?? 0],
        ];

        foreach ($budgetRows as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label . ':');
            $sheet->setCellValueExplicit('B' . $row, (float) $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValue('C' . $row, $currency);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        if (isset($summary['variance'])) {
            $row += 1;
            $sheet->setCellValue('A' . $row, 'Ecart budget:');
            $sheet->setCellValue('B' . $row, (float) $summary['variance']['variance']);
            $sheet->setCellValue('C' . $row, $currency);
            $sheet->setCellValue('D' . $row, number_format($summary['variance']['variance_percentage'], 1) . '%');
        }

        $this->applySheetStyling($sheet, 'A1:D' . max($row, 20));
    }

    protected function createExpensesSheet(Spreadsheet $spreadsheet, Installation $installation): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Depenses');

        $expenses = InstallationExpense::where('installation_id', $installation->id)
            ->with('document', 'createdBy')
            ->orderBy('date_depense')
            ->get();

        $sheet->setCellValue('A1', 'DETAIL DES DEPENSES');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:J1');

        $headers = ['Type', 'Date', 'Description', 'Fournisseur', 'Quantite', 'Prix unitaire', 'Montant total', 'TVA', 'Document', 'Cree par'];
        $this->addHeaderRow($sheet, $headers, 3);

        $row = 4;
        foreach ($expenses as $expense) {
            $sheet->setCellValue('A' . $row, $this->getExpenseTypeLabel($expense->type_depense));
            $sheet->setCellValue('B' . $row, $expense->date_depense->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $expense->description);
            $sheet->setCellValue('D' . $row, $expense->fournisseur);
            $sheet->setCellValue('E' . $row, $expense->quantite);
            $sheet->setCellValue('F' . $row, $expense->montant_unitaire);
            $sheet->setCellValue('G' . $row, $expense->montant_total);
            $sheet->setCellValue('H' . $row, $expense->tva);
            $sheet->setCellValue('I' . $row, $expense->document ? $expense->document->reference : '');
            $sheet->setCellValue('J' . $row, $expense->createdBy ? $expense->createdBy->name : '');
            $row++;
        }

        $sheet->setCellValue('F' . $row, 'TOTAL:');
        $sheet->setCellValue('G' . $row, '=SUM(G4:G' . max(4, $row - 1) . ')');
        $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);

        $this->applySheetStyling($sheet, 'A1:J' . $row);
    }

    protected function createTimePenaltySheet(Spreadsheet $spreadsheet, Installation $installation): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Temps-Penalite');

        $penalty = InstallationTimePenalty::where('installation_id', $installation->id)->first();

        $sheet->setCellValue('A1', 'PENALITES DE TEMPS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:D1');

        $rows = [
            ['Date prevue fin', $installation->planned_end_date ? $installation->planned_end_date->format('d/m/Y') : 'N/A'],
            ['Date reelle fin', $installation->actual_end_date ? $installation->actual_end_date->format('d/m/Y') : 'N/A'],
            ['Date limite contractuelle', $penalty?->date_limite_contractuelle ? $penalty->date_limite_contractuelle->format('d/m/Y') : 'N/A'],
            ['Jours de retard', $penalty?->jours_retard ?? 0],
            ['Penalite par jour', $penalty?->penalite_par_jour ?? 0],
            ['Montant penalite', $penalty?->montant_penalite ?? 0],
            ['Payeur', $penalty?->payeur ?? 'N/A'],
            ['Applicable', $penalty?->applicable ? 'Oui' : 'Non'],
            ['Raison retard', $penalty?->raison_retard ?? 'N/A'],
        ];

        $this->addKeyValueRows($sheet, $rows, 3);
        $this->applySheetStyling($sheet, 'A1:D12');
    }

    protected function createWarrantyContractSheet(Spreadsheet $spreadsheet, Installation $installation): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Garantie-Contrat');

        $equipment = $installation->equipementPrincipal;

        $sheet->setCellValue('A1', 'GARANTIE ET CONTRAT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:D1');

        $rows = $equipment ? [
            ['Equipement', $equipment->designation],
            ['Date installation', $equipment->date_installation ? $equipment->date_installation->format('d/m/Y') : 'N/A'],
            ['Date debut garantie', $equipment->date_debut_garantie ? $equipment->date_debut_garantie->format('d/m/Y') : 'N/A'],
            ['Date fin garantie', $equipment->date_fin_garantie ? $equipment->date_fin_garantie->format('d/m/Y') : 'N/A'],
            ['Statut couverture', $this->getCoverageLabel($equipment->statut_couverture)],
            ['Reference contrat', $equipment->contrat_reference ?? 'N/A'],
            ['Type de garantie', $equipment->garantie ?? 'N/A'],
        ] : [
            ['Equipement', 'Aucun equipement principal associe'],
        ];

        $this->addKeyValueRows($sheet, $rows, 3);
        $this->applySheetStyling($sheet, 'A1:D12');
    }

    protected function addHeaderRow($sheet, array $headers, int $row): void
    {
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $column++;
        }
    }

    protected function addKeyValueRows($sheet, array $rows, int $startRow): void
    {
        $row = $startRow;
        foreach ($rows as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label . ':');
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }
    }

    protected function applySheetStyling($sheet, string $range): void
    {
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function getRegimeLabel(string $regime): string
    {
        return match ($regime) {
            'garantie' => 'Sous garantie - prise en charge ST IET/Philips',
            'contrat_renouvelable' => 'Sous contrat renouvelable - client',
            'hors_contrat' => 'Hors contrat',
            default => $regime,
        };
    }

    protected function getCoverageLabel(?string $coverage): string
    {
        return match ($coverage) {
            'garantie' => 'Garantie active',
            'contrat_renouvelable' => 'Contrat renouvelable',
            'hors_contrat' => 'Hors contrat',
            'a_verifier' => 'A verifier',
            default => $coverage ?? 'N/A',
        };
    }

    protected function getExpenseTypeLabel(string $type): string
    {
        return match ($type) {
            'transport_aller' => 'Transport aller',
            'transport_retour' => 'Transport retour',
            'hotel' => 'Hotel',
            'repas' => 'Repas',
            'piece_equipement' => 'Piece equipement',
            'autre_frais' => 'Autre frais',
            default => $type,
        };
    }
}
