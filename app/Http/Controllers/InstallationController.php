<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\HistoriqueStatutInstallation;
use App\Models\Installation;
use App\Services\InstallationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstallationController extends Controller
{
    public function __construct(private readonly InstallationStatusService $statusService)
    {
    }

    public function index(Request $request)
    {
        $this->authorizeInstallation('viewAny', Installation::class);

        $query = Installation::query();

        $query->when($request->filled('profil'), fn ($q) => $q->where('type_profil', (string) $request->string('profil')));
        $query->when($request->filled('statut'), fn ($q) => $q->where('statut', (string) $request->string('statut')));
        $query->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->integer('client_id')));
        $query->when($request->filled('equipement_principal_id'), fn ($q) => $q->where('equipement_principal_id', $request->integer('equipement_principal_id')));

        if ($request->boolean('documents_manquants')) {
            $query->whereDoesntHave('documents', fn ($q) => $q->where('est_bloquant', true));
        }

        $this->applySorting($query, (string) $request->input('sort', 'recent'));

        $installations = $query->get();
        $equipements = Equipement::orderBy('code')->get();
        $currentUser = $this->effectiveUser();

        return view('installations.index', compact('installations', 'equipements', 'currentUser'));
    }

    public function create()
    {
        $this->authorizeInstallation('create', Installation::class);

        $equipements = Equipement::all();

        return view('installations.create', compact('equipements'));
    }

    public function store(Request $request)
    {
        $this->authorizeInstallation('create', Installation::class);

        $validated = $this->validatedInstallationData($request);
        $mriData = $this->validatedMriData($request);

        $installation = DB::transaction(function () use ($validated, $mriData) {
            $initialStatus = $validated['statut'];
            $validated['statut'] = InstallationStatusService::BROUILLON;

            $installation = Installation::create($validated);

            if ($installation->type_profil === Installation::TYPE_IRM) {
                $installation->profilIrm()->create($mriData);
                $installation->load('profilIrm');
            }

            if ($initialStatus !== InstallationStatusService::BROUILLON) {
                $this->statusService->assertCanTransition(
                    $installation,
                    $initialStatus,
                    $this->effectiveUser(),
                    $mriData
                );

                $installation->statut = $initialStatus;
                $installation->save();
            }

            HistoriqueStatutInstallation::create([
                'installation_id' => $installation->id,
                'user_id' => $this->effectiveUserId(),
                'ancien_statut' => '',
                'nouveau_statut' => $installation->statut,
                'commentaire' => "Création de l'installation",
            ]);

            return $installation;
        });

        return redirect()
            ->route('installations.index')
            ->with('success', 'Installation créée avec succès.');
    }

    public function show(Installation $installation)
    {
        $this->authorizeInstallation('view', $installation);

        $installation->load(['documents', 'historiqueStatuts', 'equipements', 'profilCatLab', 'profilIrm']);

        return view('installations.show', compact('installation'));
    }

    public function edit(Installation $installation)
    {
        $this->authorizeInstallation('update', $installation);

        $installation->load('profilIrm');
        $equipements = Equipement::all();

        return view('installations.edit', compact('installation', 'equipements'));
    }

    public function update(Request $request, Installation $installation)
    {
        $this->authorizeInstallation('update', $installation);

        $validated = $this->validatedInstallationData($request, $installation);
        $mriData = $this->validatedMriData($request);

        if ($installation->type_profil !== $validated['type_profil']) {
            throw ValidationException::withMessages([
                'type_profil' => 'Le changement de profil après création est bloqué dans ce POC.',
            ]);
        }

        DB::transaction(function () use ($installation, $validated, $mriData) {
            if ($installation->statut !== $validated['statut']) {
                $this->authorizeInstallation('changeStatus', $installation);
                $this->statusService->assertCanTransition(
                    $installation,
                    $validated['statut'],
                    $this->effectiveUser(),
                    $mriData
                );

                HistoriqueStatutInstallation::create([
                    'installation_id' => $installation->id,
                    'user_id' => $this->effectiveUserId(),
                    'ancien_statut' => $installation->statut,
                    'nouveau_statut' => $validated['statut'],
                    'commentaire' => 'Changement de statut via modification',
                ]);
            }

            $installation->update($validated);

            if ($installation->type_profil === Installation::TYPE_IRM) {
                $installation->profilIrm()->updateOrCreate(
                    ['installation_id' => $installation->id],
                    $mriData
                );
            }
        });

        return redirect()
            ->route('installations.show', $installation)
            ->with('success', 'Installation mise à jour avec succès.');
    }

    public function destroy(Installation $installation)
    {
        $this->authorizeInstallation('delete', $installation);

        $installation->delete();

        return redirect()
            ->route('installations.index')
            ->with('success', 'Installation supprimée avec succès.');
    }

    private function validatedInstallationData(Request $request, ?Installation $installation = null): array
    {
        $codeRule = Rule::unique('installations', 'code_installation');

        if ($installation) {
            $codeRule->ignore($installation->id);
        }

        return $request->validate([
            'code_installation' => [
                'required',
                'string',
                $codeRule,
            ],
            'nom' => 'required|string|max:255',
            'type_profil' => ['required', Rule::in(Installation::profileTypes())],
            'statut' => ['required', Rule::in(InstallationStatusService::statuses())],
            'criticite' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'equipement_principal_id' => 'nullable|integer',
            'proprietaire_interne_id' => 'nullable|integer',
        ]);
    }

    private function validatedMriData(Request $request): array
    {
        $request->validate([
            'profil_irm.champ_magnetique' => 'nullable|string|max:255',
            'profil_irm.blindage' => 'nullable|string|max:255',
            'profil_irm.atelier' => 'nullable|string|max:255',
            'profil_irm.batiment' => 'nullable|string|max:255',
            'profil_irm.etage' => 'nullable|string|max:255',
            'profil_irm.zone' => 'nullable|string|max:255',
        ]);

        $data = $request->input('profil_irm', []);

        return [
            'champ_magnetique' => $data['champ_magnetique'] ?? null,
            'zone_controlee' => (bool) $request->input('profil_irm.zone_controlee', false),
            'blindage' => $data['blindage'] ?? null,
            'atelier' => $data['atelier'] ?? null,
            'confinement_ferromagnetique' => (bool) $request->input('profil_irm.confinement_ferromagnetique', false),
            'arret_urgence' => (bool) $request->input('profil_irm.arret_urgence', false),
            'batiment' => $data['batiment'] ?? null,
            'etage' => $data['etage'] ?? null,
            'zone' => $data['zone'] ?? null,
        ];
    }

    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'nom_az' => $query->orderBy('nom')->orderBy('code_installation'),
            'nom_za' => $query->orderByDesc('nom')->orderBy('code_installation'),
            'code_az' => $query->orderBy('code_installation'),
            'code_za' => $query->orderByDesc('code_installation'),
            'profil' => $query->orderBy('type_profil')->orderBy('nom'),
            'statut_cycle' => $query
                ->orderByRaw($this->statusOrderCase())
                ->orderBy('nom'),
            'criticite_desc' => $query
                ->orderByRaw($this->criticalityOrderCase().' DESC')
                ->orderBy('nom'),
            'criticite_asc' => $query
                ->orderByRaw($this->criticalityOrderCase().' ASC')
                ->orderBy('nom'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }

    private function criticalityOrderCase(): string
    {
        return "CASE criticite WHEN 'Critique' THEN 4 WHEN 'Haute' THEN 3 WHEN 'Moyenne' THEN 2 WHEN 'Basse' THEN 1 ELSE 0 END";
    }

    private function statusOrderCase(): string
    {
        return "CASE statut WHEN 'Brouillon' THEN 1 WHEN 'En validation' THEN 2 WHEN 'Installé' THEN 3 WHEN 'Opérationnel' THEN 4 WHEN 'En maintenance' THEN 5 WHEN 'Temporairement indisponible' THEN 6 WHEN 'Archivé' THEN 7 ELSE 99 END";
    }
}
