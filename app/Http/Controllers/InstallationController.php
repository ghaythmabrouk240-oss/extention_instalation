<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Models\Equipement;
use App\Models\DocumentInstallation;
use App\Models\HistoriqueStatutInstallation;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function index()
    {
        $installations = Installation::latest()->get();
        return view('installations.index', compact('installations'));
    }

    public function create()
    {
        $equipements = Equipement::all();
        return view('installations.create', compact('equipements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_installation' => 'required|string|unique:installations,code_installation',
            'nom' => 'required|string|max:255',
            'type_profil' => 'required|in:IRM,CATHETERISME',
            'statut' => 'required|string',
            'criticite' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'equipement_principal_id' => 'nullable|integer',
            'proprietaire_interne_id' => 'nullable|integer',
        ]);

        $installation = Installation::create($validated);

        HistoriqueStatutInstallation::create([
            'installation_id' => $installation->id,
            'user_id' => auth()->id() ?? 1,
            'ancien_statut' => '',
            'nouveau_statut' => $installation->statut,
            'commentaire' => 'Création de l\'installation',
        ]);

        return redirect()->route('installations.index')->with('success', 'Installation créée avec succès.');
    }

    public function show(Installation $installation)
    {
        $installation->load(['documents', 'historiqueStatuts', 'equipements', 'profilCatLab']);
        return view('installations.show', compact('installation'));
    }

    public function edit(Installation $installation)
    {
        $equipements = Equipement::all();
        return view('installations.edit', compact('installation', 'equipements'));
    }

    public function update(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'code_installation' => 'required|string|unique:installations,code_installation,' . $installation->id,
            'nom' => 'required|string|max:255',
            'type_profil' => 'required|in:IRM,CATHETERISME',
            'statut' => 'required|string',
            'criticite' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'equipement_principal_id' => 'nullable|integer',
            'proprietaire_interne_id' => 'nullable|integer',
        ]);

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

        return redirect()->route('installations.show', $installation)->with('success', 'Installation mise à jour avec succès.');
    }

    public function destroy(Installation $installation)
    {
        $installation->delete();
        return redirect()->route('installations.index')->with('success', 'Installation supprimée avec succès.');
    }
}
