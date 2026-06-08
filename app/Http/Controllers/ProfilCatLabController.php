<?php

namespace App\Http\Controllers;

use App\Models\ProfilCatLab;
use App\Models\Installation;
use Illuminate\Http\Request;

class ProfilCatLabController extends Controller
{
    public function index()
    {
        $profils = ProfilCatLab::with('installation')->latest()->get();
        return view('profil-cat-labs.index', compact('profils'));
    }

    public function create()
    {
        $installations = Installation::where('type_profil', 'CATHETERISME')->doesntHave('profilCatLab')->get();
        return view('profil-cat-labs.create', compact('installations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'systeme_angiographie' => 'required|string|max:255',
            'radioprotection' => 'required|string|max:255',
            'injecteur' => 'required|string|max:255',
            'moniteurs' => 'required|string|max:255',
            'controle_acces' => 'boolean',
            'table_patient' => 'required|string|max:255',
        ]);

        $validated['controle_acces'] = $request->has('controle_acces');

        ProfilCatLab::create($validated);

        return redirect()->route('profil-cat-labs.index')->with('success', 'Profil CathLab créé avec succès.');
    }

    public function show(ProfilCatLab $profilCatLab)
    {
        $profilCatLab->load('installation');
        return view('profil-cat-labs.show', compact('profilCatLab'));
    }

    public function edit(ProfilCatLab $profilCatLab)
    {
        $installations = Installation::where('type_profil', 'CATHETERISME')->get();
        return view('profil-cat-labs.edit', compact('profilCatLab', 'installations'));
    }

    public function update(Request $request, ProfilCatLab $profilCatLab)
    {
        $validated = $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'systeme_angiographie' => 'required|string|max:255',
            'radioprotection' => 'required|string|max:255',
            'injecteur' => 'required|string|max:255',
            'moniteurs' => 'required|string|max:255',
            'controle_acces' => 'boolean',
            'table_patient' => 'required|string|max:255',
        ]);

        $validated['controle_acces'] = $request->has('controle_acces');

        $profilCatLab->update($validated);

        return redirect()->route('profil-cat-labs.show', $profilCatLab)->with('success', 'Profil CathLab mis à jour avec succès.');
    }

    public function destroy(ProfilCatLab $profilCatLab)
    {
        $profilCatLab->delete();
        return redirect()->route('profil-cat-labs.index')->with('success', 'Profil CathLab supprimé avec succès.');
    }
}
