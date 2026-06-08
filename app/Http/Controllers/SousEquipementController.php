<?php

namespace App\Http\Controllers;

use App\Models\SousEquipement;
use App\Models\Equipement;
use Illuminate\Http\Request;

class SousEquipementController extends Controller
{
    public function index()
    {
        $sousEquipements = SousEquipement::with('equipement')->latest()->get();
        return view('sous-equipements.index', compact('sousEquipements'));
    }

    public function create()
    {
        $equipements = Equipement::all();
        return view('sous-equipements.create', compact('equipements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identifiant' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'description' => 'required|string',
            'equipement_id' => 'required|exists:equipements,id',
        ]);

        SousEquipement::create($validated);

        return redirect()->route('sous-equipements.index')->with('success', 'Sous-équipement créé avec succès.');
    }

    public function show(SousEquipement $sousEquipement)
    {
        $sousEquipement->load('equipement');
        return view('sous-equipements.show', compact('sousEquipement'));
    }

    public function edit(SousEquipement $sousEquipement)
    {
        $equipements = Equipement::all();
        return view('sous-equipements.edit', compact('sousEquipement', 'equipements'));
    }

    public function update(Request $request, SousEquipement $sousEquipement)
    {
        $validated = $request->validate([
            'identifiant' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'description' => 'required|string',
            'equipement_id' => 'required|exists:equipements,id',
        ]);

        $sousEquipement->update($validated);

        return redirect()->route('sous-equipements.show', $sousEquipement)->with('success', 'Sous-équipement mis à jour avec succès.');
    }

    public function destroy(SousEquipement $sousEquipement)
    {
        $sousEquipement->delete();
        return redirect()->route('sous-equipements.index')->with('success', 'Sous-équipement supprimé avec succès.');
    }
}
