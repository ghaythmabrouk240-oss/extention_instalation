<?php

namespace App\Http\Controllers;

use App\Models\DocumentInstallation;
use App\Models\Installation;
use Illuminate\Http\Request;

class DocumentInstallationController extends Controller
{
    public function index()
    {
        $documents = DocumentInstallation::with('installation')->latest()->get();
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $installations = Installation::all();
        return view('documents.create', compact('installations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'categorie' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'est_bloquant' => 'boolean',
        ]);

        $validated['est_bloquant'] = $request->has('est_bloquant');

        DocumentInstallation::create($validated);

        return redirect()->route('documents.index')->with('success', 'Document créé avec succès.');
    }

    public function show(DocumentInstallation $document)
    {
        $document->load('installation');
        return view('documents.show', compact('document'));
    }

    public function edit(DocumentInstallation $document)
    {
        $installations = Installation::all();
        return view('documents.edit', compact('document', 'installations'));
    }

    public function update(Request $request, DocumentInstallation $document)
    {
        $validated = $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'categorie' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'est_bloquant' => 'boolean',
        ]);

        $validated['est_bloquant'] = $request->has('est_bloquant');

        $document->update($validated);

        return redirect()->route('documents.show', $document)->with('success', 'Document mis à jour avec succès.');
    }

    public function destroy(DocumentInstallation $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document supprimé avec succès.');
    }
}
