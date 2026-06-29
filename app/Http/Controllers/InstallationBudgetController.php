<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Models\InstallationBudget;
use App\Models\InstallationExpense;
use App\Models\InstallationTimePenalty;
use App\Services\InstallationBudgetService;
use App\Services\InstallationInvoiceExportService;
use Illuminate\Http\Request;

class InstallationBudgetController extends Controller
{
    protected $budgetService;

    public function __construct(InstallationBudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Display budget summary for an installation
     */
    public function show(Installation $installation)
    {
        $budget = InstallationBudget::firstOrNew(
            ['installation_id' => $installation->id],
            [
                'regime_prise_en_charge' => 'garantie',
                'devise' => 'EUR',
                'budget_prevu' => 0,
                'total_frais' => 0,
                'total_penalites' => 0,
                'total_final' => 0,
                'statut_validation' => 'brouillon',
            ]
        );
        $expenses = InstallationExpense::where('installation_id', $installation->id)
            ->with('document', 'createdBy')
            ->latest()
            ->get();
        $timePenalty = InstallationTimePenalty::where('installation_id', $installation->id)->first();
        
        $summary = $this->budgetService->getBudgetSummary($installation->id);

        return view('installations.budget', compact(
            'installation',
            'budget',
            'expenses',
            'timePenalty',
            'summary'
        ));
    }

    /**
     * Store a new expense
     */
    public function storeExpense(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'type_depense' => 'required|in:transport_aller,transport_retour,hotel,repas,piece_equipement,autre_frais',
            'date_depense' => 'required|date',
            'description' => 'required|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'quantite' => 'required|numeric|min:0',
            'montant_unitaire' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0',
            'document_id' => 'nullable|exists:document_installations,id',
        ]);

        $validated['installation_id'] = $installation->id;
        $validated['montant_total'] = $validated['quantite'] * $validated['montant_unitaire'];
        $validated['created_by'] = auth()->id();

        InstallationExpense::create($validated);

        // Update budget totals
        $this->budgetService->updateBudgetTotals($installation->id);

        return redirect()->route('installations.budget', $installation)
            ->with('success', 'Dépense ajoutée avec succès.');
    }

    /**
     * Update budget information
     */
    public function updateBudget(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'regime_prise_en_charge' => 'required|in:garantie,contrat_renouvelable,hors_contrat',
            'budget_prevu' => 'nullable|numeric|min:0',
            'devise' => 'required|in:EUR,TND',
            'reference_contrat' => 'nullable|string|max:255',
            'notes_finance' => 'nullable|string',
            'statut_validation' => 'required|in:brouillon,en_cours,valide,rejete',
        ]);

        $budget = InstallationBudget::updateOrCreate(
            ['installation_id' => $installation->id],
            $validated
        );

        $this->budgetService->updateBudgetTotals($installation->id);

        return redirect()->route('installations.budget', $installation)
            ->with('success', 'Budget mis à jour avec succès.');
    }

    /**
     * Update time penalty
     */
    public function updateTimePenalty(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'date_limite_contractuelle' => 'nullable|date',
            'penalite_par_jour' => 'required|numeric|min:0',
            'payeur' => 'required|string|max:255',
            'raison_retard' => 'nullable|string',
            'applicable' => 'nullable|boolean',
        ]);

        // Handle checkbox - if not sent, it's false
        $validated['applicable'] = $request->has('applicable');

        // Calculate penalty if actual end date exists
        if ($installation->actual_end_date && $validated['date_limite_contractuelle']) {
            $penaltyCalc = $this->budgetService->calculateDelayPenalty(
                $installation->id,
                \Carbon\Carbon::parse($installation->actual_end_date),
                $validated['penalite_par_jour']
            );
            $validated['jours_retard'] = $penaltyCalc['jours_retard'];
            $validated['montant_penalite'] = $penaltyCalc['montant_penalite'];
        }

        InstallationTimePenalty::updateOrCreate(
            ['installation_id' => $installation->id],
            $validated
        );

        $this->budgetService->updateBudgetTotals($installation->id);

        return redirect()->route('installations.budget', $installation)
            ->with('success', 'Pénalité de temps mise à jour avec succès.');
    }

    /**
     * Export budget and expenses for an installation.
     */
    public function export(Installation $installation, InstallationInvoiceExportService $exportService)
    {
        $filePath = $exportService->exportInvoice($installation);

        $filename = sprintf(
            'budget-%s-%s-%s.xlsx',
            $installation->code_installation,
            now()->format('Y-m-d'),
            now()->format('H-i-s')
        );

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Delete an expense
     */
    public function destroyExpense(Installation $installation, InstallationExpense $expense)
    {
        if ($expense->installation_id !== $installation->id) {
            abort(403);
        }

        $expense->delete();

        $this->budgetService->updateBudgetTotals($installation->id);

        return redirect()->route('installations.budget', $installation)
            ->with('success', 'Dépense supprimée avec succès.');
    }
}
