<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallationGraphRequest;
use App\Models\Installation;
use App\Services\Graph\ReadinessStrategyFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstallationGraphController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $installations = Installation::all();
        $selectedInstallationId = $request->query('installation_id');

        return view('installations.graph', compact('installations', 'selectedInstallationId'));
    }

    public function show(InstallationGraphRequest $request): JsonResponse
    {
        $installation = Installation::with([
            'profilCatLab',
            'equipementPrincipal',
            'equipements',
            'documents'
        ])->findOrFail($request->installation_id);

        // TODO: Re-enable authorization when authentication is set up
        // $this->authorize('view', $installation);

        $strategy = ReadinessStrategyFactory::make($request->profile);
        $graph = $strategy->buildGraph($installation);

        return response()->json($graph);
    }
}
