<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Equipement;
use App\Models\HistoriqueStatutInstallation;
use App\Models\Installation;
use App\Models\User;
use App\Services\ExcelExportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $query = Installation::with(['equipementPrincipal', 'documents', 'client']);

        if ($request->filled('type_profil')) {
            $query->where('type_profil', $request->input('type_profil'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('code_installation', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        $installations = $query->latest()->get();

        if ($request->boolean('export')) {
            return $this->exportList($installations);
        }

        if ($request->filled('documents_manquants')) {
            $installations = $installations->filter(fn (Installation $installation) => $installation->missingRequiredDocumentCategories() !== []);
        }

        return view('installations.index', [
            'installations' => $installations,
            'clients' => Client::orderBy('nom')->get(),
            'statuses' => Installation::standardStatuses(),
            'filters' => $request->only(['type_profil', 'statut', 'client_id', 'search', 'documents_manquants']),
        ]);
    }

    public function calendar(Request $request)
    {
        $month = Carbon::parse($request->input('month', now()->format('Y-m')) . '-01')->startOfMonth();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $calendarStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $end->copy()->endOfWeek(Carbon::SUNDAY);

        $installations = $this->installationsOverlappingPeriod($calendarStart, $calendarEnd);
        $interventions = HistoriqueStatutInstallation::with('installation')
            ->whereBetween('created_at', [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()])
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn (HistoriqueStatutInstallation $item) => $item->created_at->format('Y-m-d'));

        $weeks = [];
        $cursor = $calendarStart->copy();
        $monthStats = ['installations' => 0, 'interventions' => 0];

        while ($cursor->lte($calendarEnd)) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $events = $this->eventsForDay($cursor, $installations, $interventions->get($cursor->format('Y-m-d'), collect()));

                if ($cursor->month === $month->month) {
                    foreach ($events as $event) {
                        if ($event['type'] === 'intervention') {
                            $monthStats['interventions']++;
                        } elseif ($event['isStart'] ?? false) {
                            $monthStats['installations']++;
                        }
                    }
                }

                $week[] = [
                    'date' => $cursor->copy(),
                    'isCurrentMonth' => $cursor->month === $month->month,
                    'isToday' => $cursor->isToday(),
                    'events' => $events,
                ];
                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return view('installations.calendar', [
            'weeks' => $weeks,
            'month' => $month,
            'previousMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
            'monthStats' => $monthStats,
        ]);
    }

    public function create()
    {
        $equipements = Equipement::orderBy('code')->get();
        $clients = Client::orderBy('nom')->get();
        $users = User::orderBy('name')->get();

        return view('installations.create', compact('equipements', 'clients', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateInstallation($request);
        $cathProfile = $this->validateCathProfile($request, $validated['type_profil']);
        $secondaryEquipements = $validated['equipements_secondaires'] ?? [];
        unset($validated['equipements_secondaires']);

        $installation = DB::transaction(function () use ($validated, $cathProfile, $secondaryEquipements) {
            $installation = Installation::create($validated);

            if ($installation->type_profil === 'CATHETERISME') {
                $installation->profilCatLab()->create($cathProfile);
            }

            $this->syncSecondaryEquipements($installation, $secondaryEquipements);

            HistoriqueStatutInstallation::create([
                'installation_id' => $installation->id,
                'user_id' => auth()->id() ?? 1,
                'ancien_statut' => '',
                'nouveau_statut' => $installation->statut,
                'commentaire' => 'Creation de l installation',
            ]);

            return $installation;
        });

        return redirect()->route('installations.show', $installation)->with('success', 'Installation creee avec succes.');
    }

    public function show(Installation $installation)
    {
        $installation->load([
            'documents',
            'historiqueStatuts',
            'equipements.sousEquipements',
            'equipementPrincipal.sousEquipements',
            'profilCatLab',
            'profilIrm',
            'client',
            'proprietaireInterne',
        ]);

        return view('installations.show', [
            'installation' => $installation,
            'missingRequiredDocuments' => $installation->missingRequiredDocumentCategories(),
            'uploadableReports' => collect(Installation::uploadableReportCategories())
                ->map(fn (array $report) => array_merge($report, [
                    'document' => $installation->activeDocumentByCategorie($report['categorie']),
                ])),
        ]);
    }

    public function export(Installation $installation)
    {
        $excelService = new ExcelExportService();
        $filePath = $excelService->exportInstallation($installation);
        
        $filename = sprintf(
            'installation-%s-%s.xlsx',
            $installation->code_installation,
            now()->format('Y-m-d')
        );

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    public function edit(Installation $installation)
    {
        $installation->load('profilCatLab', 'equipements');
        $equipements = Equipement::orderBy('code')->get();
        $clients = Client::orderBy('nom')->get();
        $users = User::orderBy('name')->get();

        return view('installations.edit', compact('installation', 'equipements', 'clients', 'users'));
    }

    public function update(Request $request, Installation $installation)
    {
        $validated = $this->validateInstallation($request, $installation);
        $cathProfile = $this->validateCathProfile($request, $validated['type_profil']);
        $secondaryEquipements = $validated['equipements_secondaires'] ?? [];
        unset($validated['equipements_secondaires']);

        DB::transaction(function () use ($installation, $validated, $cathProfile, $secondaryEquipements) {
            if ($installation->statut !== $validated['statut']) {
                HistoriqueStatutInstallation::create([
                    'installation_id' => $installation->id,
                    'user_id' => auth()->id() ?? 1,
                    'ancien_statut' => $installation->statut,
                    'nouveau_statut' => $validated['statut'],
                    'commentaire' => 'Changement de statut via modification',
                ]);
            }

            $installation->update($validated);

            if ($installation->type_profil === 'CATHETERISME') {
                $installation->profilCatLab()->updateOrCreate(
                    ['installation_id' => $installation->id],
                    $cathProfile
                );
            } else {
                $installation->profilCatLab()->delete();
            }

            $this->syncSecondaryEquipements($installation, $secondaryEquipements);
        });

        return redirect()->route('installations.show', $installation)->with('success', 'Installation mise a jour avec succes.');
    }

    public function destroy(Installation $installation)
    {
        $installation->delete();

        return redirect()->route('installations.index')->with('success', 'Installation supprimee avec succes.');
    }

    private function validateInstallation(Request $request, ?Installation $installation = null): array
    {
        $installationId = $installation?->id;

        return $request->validate([
            'code_installation' => 'required|string|unique:installations,code_installation,' . $installationId,
            'nom' => 'required|string|max:255',
            'type_profil' => 'required|in:IRM,CATHETERISME',
            'statut' => 'required|in:' . implode(',', Installation::standardStatuses()),
            'criticite' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'equipement_principal_id' => 'nullable|exists:equipements,id',
            'proprietaire_interne_id' => 'nullable|exists:users,id',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'calendar_note' => 'nullable|string',
            'equipements_secondaires' => 'array',
            'equipements_secondaires.*' => 'exists:equipements,id',
        ]);
    }

    private function validateCathProfile(Request $request, string $typeProfil): array
    {
        if ($typeProfil !== 'CATHETERISME') {
            return [];
        }

        $validated = $request->validate([
            'departement' => 'required|string|max:255',
            'batiment' => 'required|string|max:255',
            'etage' => 'required|string|max:255',
            'systeme_angiographie' => 'required|string|max:255',
            'station_controle' => 'required|string|max:255',
            'radioprotection' => 'required|string|max:255',
            'injecteur' => 'required|string|max:255',
            'moniteurs' => 'required|string|max:255',
            'controle_acces' => 'boolean',
            'table_patient' => 'required|string|max:255',
            'alimentation' => 'required|string|max:255',
            'reseau' => 'required|string|max:255',
            'ventilation' => 'required|string|max:255',
            'protection_murale' => 'required|string|max:255',
            'stockage_consommables' => 'required|string|max:255',
            'signalisation_rayonnement' => 'required|string|max:255',
            'conformite_salle_interventionnelle' => 'required|string|max:255',
            'dispositifs_securite' => 'required|string|max:255',
        ]);

        $validated['controle_acces'] = $request->boolean('controle_acces');

        return $validated;
    }

    private function syncSecondaryEquipements(Installation $installation, array $equipementIds): void
    {
        $syncPayload = collect($equipementIds)
            ->filter()
            ->reject(fn ($id) => (int) $id === (int) $installation->equipement_principal_id)
            ->unique()
            ->mapWithKeys(fn ($id) => [(int) $id => ['role' => 'secondaire']])
            ->all();

        $installation->equipements()->sync($syncPayload);
    }

    private function installationsOverlappingPeriod(Carbon $periodStart, Carbon $periodEnd): Collection
    {
        return Installation::with('client')
            ->where(function (Builder $query) use ($periodStart, $periodEnd) {
                $this->applyDateRangeOverlap($query, 'planned_start_date', 'planned_end_date', $periodStart, $periodEnd);
                $query->orWhere(function (Builder $query) use ($periodStart, $periodEnd) {
                    $this->applyDateRangeOverlap($query, 'actual_start_date', 'actual_end_date', $periodStart, $periodEnd);
                });
            })
            ->orderBy('planned_start_date')
            ->orderBy('code_installation')
            ->get();
    }

    private function applyDateRangeOverlap(
        Builder $query,
        string $startColumn,
        string $endColumn,
        Carbon $periodStart,
        Carbon $periodEnd
    ): void {
        $query->whereNotNull($startColumn)
            ->where($startColumn, '<=', $periodEnd->toDateString())
            ->where(function (Builder $query) use ($endColumn, $periodStart) {
                $query->whereNull($endColumn)
                    ->orWhere($endColumn, '>=', $periodStart->toDateString());
            });
    }

    private function eventsForDay(Carbon $day, Collection $installations, Collection $interventions): array
    {
        $events = [];
        $dayString = $day->toDateString();

        foreach ($installations as $installation) {
            if ($this->dayInDateRange($day, $installation->planned_start_date, $installation->planned_end_date)) {
                $events[] = [
                    'type' => 'installation_planned',
                    'installation' => $installation,
                    'isStart' => $installation->planned_start_date?->toDateString() === $dayString,
                ];
            }

            if ($this->dayInDateRange($day, $installation->actual_start_date, $installation->actual_end_date)) {
                $events[] = [
                    'type' => 'installation_actual',
                    'installation' => $installation,
                    'isStart' => $installation->actual_start_date?->toDateString() === $dayString,
                ];
            }
        }

        foreach ($interventions as $intervention) {
            $events[] = [
                'type' => 'intervention',
                'intervention' => $intervention,
                'isStart' => true,
            ];
        }

        return $events;
    }

    private function dayInDateRange(Carbon $day, ?Carbon $start, ?Carbon $end): bool
    {
        if ($start === null) {
            return false;
        }

        $end = $end ?? $start;

        return $day->betweenIncluded($start->copy()->startOfDay(), $end->copy()->endOfDay());
    }

    private function exportList(Collection $installations)
    {
        $excelService = new ExcelExportService();
        $filePath = $excelService->exportInstallationsList($installations);
        
        $filename = 'installations-' . now()->format('Y-m-d') . '.xlsx';

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
}
