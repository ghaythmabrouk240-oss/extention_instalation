<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\Installation;

class ExcelExportService
{
    private Spreadsheet $spreadsheet;
    private $sheet;
    private $row = 1;

    // Site colors matching gmao.css
    private const PRIMARY_BLUE = '1565C0';
    private const SECONDARY_BLUE = '1976D2';
    private const DARK_BLUE = '0D47A1';
    private const LIGHT_BLUE = 'E3F2FD';
    private const LIGHT_BLUE_ALT = 'BBDEFB';
    private const GRAY_LIGHT = 'F5F5F5';
    private const GRAY_BORDER = 'DDDDDD';
    private const WHITE = 'FFFFFF';
    private const TEXT_DARK = '333333';
    private const TEXT_GRAY = '666666';

    // Status colors matching site badges
    private const STATUS_BROUILLON = '9E9E9E';
    private const STATUS_EN_VALIDATION = 'FF9800';
    private const STATUS_INSTALLE = '03A9F4';
    private const STATUS_OPERATIONNEL = '4CAF50';
    private const STATUS_EN_MAINTENANCE = 'FF5722';
    private const STATUS_INDISPONIBLE = 'F44336';
    private const STATUS_ARCHIVE = '607D8B';

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    public function exportInstallation(Installation $installation): string
    {
        $this->row = 1;
        $installation->load([
            'documents',
            'historiqueStatuts',
            'equipements',
            'equipementPrincipal',
            'profilCatLab',
            'profilIrm',
            'client',
        ]);

        // Title
        $this->addTitle("Installation: {$installation->code_installation} - {$installation->nom}");

        // Identity Section
        $this->addSectionHeader('IDENTITE');
        $this->addKeyValueRow('Code installation', $installation->code_installation);
        $this->addKeyValueRow('Nom', $installation->nom);
        $this->addKeyValueRow('Type profil', $installation->type_profil);
        $this->addKeyValueRow('Statut', $installation->statut, $this->getStatusColor($installation->statut));
        $this->addKeyValueRow('Criticite', $installation->criticite ?? 'Non defini');
        $this->addKeyValueRow('Client', $installation->client?->nom ?? 'Non defini');
        $this->addKeyValueRow('Equipement principal', $installation->equipementPrincipal
            ? $installation->equipementPrincipal->code . ' - ' . $installation->equipementPrincipal->designation
            : 'Non defini');
        $this->addKeyValueRow('Note calendrier', $installation->calendar_note ?? 'Non defini');

        // Planning Section
        $this->addSectionHeader('PLANIFICATION');
        $this->addKeyValueRow('Debut prevu', optional($installation->planned_start_date)->format('d/m/Y') ?? 'Non defini');
        $this->addKeyValueRow('Fin prevue', optional($installation->planned_end_date)->format('d/m/Y') ?? 'Non defini');
        $this->addKeyValueRow('Debut reel', optional($installation->actual_start_date)->format('d/m/Y') ?? 'Non defini');
        $this->addKeyValueRow('Fin reelle', optional($installation->actual_end_date)->format('d/m/Y') ?? 'Non defini');

        // Catheterisme Profile Section
        if ($installation->type_profil === 'CATHETERISME' && $installation->profilCatLab) {
            $cat = $installation->profilCatLab;
            $this->addSectionHeader('PROFIL CATHETERISME');
            $this->addKeyValueRow('Departement', $cat->departement);
            $this->addKeyValueRow('Batiment', $cat->batiment);
            $this->addKeyValueRow('Etage', $cat->etage);
            $this->addKeyValueRow('Systeme angiographie', $cat->systeme_angiographie);
            $this->addKeyValueRow('Station de controle', $cat->station_controle);
            $this->addKeyValueRow('Table patient', $cat->table_patient);
            $this->addKeyValueRow('Injecteur', $cat->injecteur);
            $this->addKeyValueRow('Moniteurs', $cat->moniteurs);
            $this->addKeyValueRow('Alimentation', $cat->alimentation);
            $this->addKeyValueRow('Reseau', $cat->reseau);
            $this->addKeyValueRow('Ventilation', $cat->ventilation);
            $this->addKeyValueRow('Radioprotection', $cat->radioprotection);
            $this->addKeyValueRow('Protection murale', $cat->protection_murale);
            $this->addKeyValueRow('Stockage consommables', $cat->stockage_consommables);
            $this->addKeyValueRow('Signalisation rayonnement', $cat->signalisation_rayonnement);
            $this->addKeyValueRow('Controle acces', $cat->controle_acces ? 'Oui' : 'Non');
            $this->addKeyValueRow('Conformite salle interventionnelle', $cat->conformite_salle_interventionnelle);
            $this->addKeyValueRow('Dispositifs securite', $cat->dispositifs_securite);
        }

        // Secondary Equipments Section
        $this->addSectionHeader('EQUIPEMENTS SECONDAIRES');
        $headers = ['Code', 'Designation', 'Marque', 'Modele', 'Role'];
        $data = [];
        foreach ($installation->equipements as $equipement) {
            $data[] = [
                $equipement->code,
                $equipement->designation,
                $equipement->marque,
                $equipement->modele,
                $equipement->pivot->role,
            ];
        }
        $this->addTable($headers, $data);

        // Documents Section
        $this->addSectionHeader('DOCUMENTS ET RAPPORTS');
        $headers = ['Categorie', 'Type', 'Version', 'Statut', 'Fichier', 'Actif'];
        $data = [];
        foreach ($installation->documents as $document) {
            $data[] = [
                $document->categorie,
                $document->type_rapport ?? '-',
                $document->version,
                $document->statut,
                $document->fichier_original_name ?? '-',
                $document->est_version_active ? 'Oui' : 'Non',
            ];
        }
        $this->addTable($headers, $data);

        // History Section
        $this->addSectionHeader('HISTORIQUE STATUTS');
        $headers = ['Date', 'Ancien statut', 'Nouveau statut', 'Commentaire'];
        $data = [];
        foreach ($installation->historiqueStatuts as $historique) {
            $data[] = [
                $historique->created_at->format('d/m/Y H:i'),
                $historique->ancien_statut ?: '-',
                $historique->nouveau_statut,
                $historique->commentaire,
            ];
        }
        $this->addTable($headers, $data);

        // Auto-size columns
        foreach (range('A', $this->sheet->getHighestColumn()) as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->saveToFile("installation-{$installation->code_installation}-" . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportInstallationsList($installations): string
    {
        $this->row = 1;

        // Title
        $this->addTitle('Liste des Installations');

        // Table
        $headers = ['Code', 'Nom', 'Profil', 'Client', 'Equipement principal', 'Statut', 'Criticite', 'Debut prevu', 'Docs requis manquants'];
        $data = [];
        foreach ($installations as $installation) {
            $data[] = [
                $installation->code_installation,
                $installation->nom,
                $installation->type_profil,
                $installation->client?->nom ?? 'Non defini',
                $installation->equipementPrincipal?->code ?? 'Non defini',
                $installation->statut,
                $installation->criticite ?? 'Non defini',
                optional($installation->planned_start_date)->format('d/m/Y') ?? 'Non defini',
                implode(' | ', $installation->missingRequiredDocumentCategories()),
            ];
        }
        $this->addTable($headers, $data);

        // Auto-size columns
        foreach (range('A', $this->sheet->getHighestColumn()) as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->saveToFile('installations-' . now()->format('Y-m-d') . '.xlsx');
    }

    private function addTitle(string $text): void
    {
        $this->sheet->setCellValue('A' . $this->row, $text);
        $this->sheet->getStyle('A' . $this->row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => self::PRIMARY_BLUE],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $this->row += 2;
    }

    private function addSectionHeader(string $text): void
    {
        $this->sheet->setCellValue('A' . $this->row, $text);
        $this->sheet->getStyle('A' . $this->row . ':B' . $this->row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => self::PRIMARY_BLUE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => self::LIGHT_BLUE],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::GRAY_BORDER],
                ],
            ],
        ]);
        $this->row++;
    }

    private function addKeyValueRow(string $key, string $value, ?string $statusColor = null): void
    {
        $this->sheet->setCellValue('A' . $this->row, $key);
        $this->sheet->setCellValue('B' . $this->row, $value);

        $style = [
            'font' => [
                'size' => 10,
                'color' => ['rgb' => self::TEXT_DARK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::GRAY_BORDER],
                ],
            ],
        ];

        // Key column styling
        $this->sheet->getStyle('A' . $this->row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => self::TEXT_GRAY],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => self::GRAY_LIGHT],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::GRAY_BORDER],
                ],
            ],
        ]);

        // Value column styling
        if ($statusColor) {
            $style['fill'] = [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => $statusColor],
            ];
            $style['font']['color'] = ['rgb' => self::WHITE];
            $style['font']['bold'] = true;
        }

        $this->sheet->getStyle('B' . $this->row)->applyFromArray($style);
        $this->row++;
    }

    private function addTable(array $headers, array $data): void
    {
        $col = 'A';
        $headerRow = $this->row;

        // Add headers
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . $headerRow, $header);
            $col++;
        }

        // Style headers with blue gradient effect
        $lastCol = chr(ord('A') + count($headers) - 1);
        $this->sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => self::WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => self::PRIMARY_BLUE],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::GRAY_BORDER],
                ],
            ],
        ]);

        $this->row++;

        // Add data rows
        $rowNum = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $cell) {
                $this->sheet->setCellValue($col . $this->row, $cell);
                
                // Alternate row colors
                $bgColor = ($rowNum % 2 === 0) ? self::WHITE : self::GRAY_LIGHT;
                
                $this->sheet->getStyle($col . $this->row)->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => self::TEXT_DARK],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => $bgColor],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => self::GRAY_BORDER],
                        ],
                    ],
                ]);
                $col++;
            }
            $this->row++;
            $rowNum++;
        }

        $this->row++;
    }

    private function getStatusColor(string $status): ?string
    {
        return match (strtolower($status)) {
            'brouillon' => self::STATUS_BROUILLON,
            'en validation' => self::STATUS_EN_VALIDATION,
            'installe' => self::STATUS_INSTALLE,
            'operationnel' => self::STATUS_OPERATIONNEL,
            'en maintenance' => self::STATUS_EN_MAINTENANCE,
            'temporairement indisponible', 'indisponible' => self::STATUS_INDISPONIBLE,
            'archive' => self::STATUS_ARCHIVE,
            default => null,
        };
    }

    private function saveToFile(string $filename): string
    {
        $filePath = storage_path('app/' . $filename);
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }
}
