<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\SousEquipement;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    public function index()
    {
        $equipements = Equipement::latest()->get();
        return view('equipements.index', compact('equipements'));
    }

    public function create()
    {
        $clients = \App\Models\Client::orderBy('nom')->get();
        return view('equipements.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'numero_equipement' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'modalite_id' => 'required|integer',
            'client_id' => 'required|integer',
            'software' => 'required|string|max:255',
            'date_installation' => 'required|date',
            'date_debut_garantie' => 'required|date',
            'plan_prev' => 'required|integer',
            'garantie' => 'required|string|max:255',
        ]);

        Equipement::create($validated);

        return redirect()->route('equipements.index')->with('success', 'Équipement créé avec succès.');
    }

    public function show(Equipement $equipement)
    {
        $equipement->load(['sousEquipements', 'installations']);
        return view('equipements.show', compact('equipement'));
    }

    public function edit(Equipement $equipement)
    {
        return view('equipements.edit', compact('equipement'));
    }

    public function update(Request $request, Equipement $equipement)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'numero_equipement' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'modalite_id' => 'required|integer',
            'client_id' => 'required|integer',
            'software' => 'required|string|max:255',
            'date_installation' => 'required|date',
            'date_debut_garantie' => 'required|date',
            'plan_prev' => 'required|integer',
            'garantie' => 'required|string|max:255',
        ]);

        $equipement->update($validated);

        return redirect()->route('equipements.show', $equipement)->with('success', 'Équipement mis à jour avec succès.');
    }

    public function destroy(Equipement $equipement)
    {
        $equipement->delete();
        return redirect()->route('equipements.index')->with('success', 'Équipement supprimé avec succès.');
    }
}
