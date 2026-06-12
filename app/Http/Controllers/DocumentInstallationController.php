<?php

namespace App\Http\Controllers;

use App\Models\DocumentInstallation;
use App\Models\Installation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentInstallationController extends Controller
{
    public function index()
    {
        $documents = DocumentInstallation::with('installation')->latest()->get();

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $installations = Installation::orderBy('code_installation')->get();

        return view('documents.create', [
            'installations' => $installations,
            'selectedInstallationId' => request('installation_id'),
            'selectedCategorie' => request('categorie'),
            'selectedTypeRapport' => request('type_rapport'),
            'selectedRedirectTo' => request('redirect_to'),
            'requiresFile' => request()->boolean('requires_file'),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateDocument($request);
            $validated['est_bloquant'] = $request->boolean('est_bloquant');
            $validated['est_version_active'] = $request->boolean('est_version_active', true);
            $validated = $this->storeUploadedReport($request, $validated);

            DB::transaction(function () use ($validated) {
                $this->deactivatePreviousActiveVersions($validated);
                DocumentInstallation::create($validated);
            });

            return $this->redirectAfterSave($request, (int) $validated['installation_id'], 'Document cree avec succes.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la creation du document: ' . $e->getMessage());
        }
    }

    public function show(DocumentInstallation $document)
    {
        $document->load('installation');

        return view('documents.show', compact('document'));
    }

    public function edit(DocumentInstallation $document)
    {
        $installations = Installation::orderBy('code_installation')->get();

        return view('documents.edit', compact('document', 'installations'))->with('test_message', 'Controller updated at ' . now());
    }

    public function update(Request $request, DocumentInstallation $document)
    {
        try {
            $validated = $this->validateDocument($request);
            $validated['est_bloquant'] = $request->boolean('est_bloquant');
            $validated['est_version_active'] = $request->boolean('est_version_active');
            $validated = $this->storeUploadedReport($request, $validated, $document);

            DB::transaction(function () use ($document, $validated) {
                $this->deactivatePreviousActiveVersions($validated, $document);
                $document->update($validated);
            });

            return redirect()->route('documents.show', $document)->with('success', 'Document mis a jour avec succes.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Erreur de validation');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function destroy(DocumentInstallation $document)
    {
        if ($document->fichier_path) {
            Storage::disk('public')->delete($document->fichier_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document supprime avec succes.');
    }

    private function validateDocument(Request $request): array
    {
        $fileRules = $request->boolean('requires_file')
            ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';

        return $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'categorie' => 'required|string|max:255',
            'type_rapport' => 'nullable|in:installation_generale,rapport_tests,document_requis,rapport_technique',
            'version' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'description' => 'nullable|string',
            'est_bloquant' => 'nullable|boolean',
            'reference_dms' => 'nullable|string|max:255',
            'reference_fichier' => 'nullable|string|max:255',
            'fichier' => $fileRules,
            'profil_concerne' => 'required|in:IRM,CATHETERISME,COMMUN',
            'est_version_active' => 'nullable|boolean',
            'redirect_to' => 'nullable|string',
        ]);
    }

    private function storeUploadedReport(Request $request, array $validated, ?DocumentInstallation $document = null): array
    {
        unset($validated['fichier']);

        if (! $request->hasFile('fichier')) {
            return $validated;
        }

        if ($document?->fichier_path) {
            Storage::disk('public')->delete($document->fichier_path);
        }

        $file = $request->file('fichier');
        $validated['fichier_path'] = $file->store('installation-reports', 'public');
        $validated['fichier_original_name'] = $file->getClientOriginalName();
        $validated['fichier_mime_type'] = $file->getMimeType();

        return $validated;
    }

    private function redirectAfterSave(Request $request, int $installationId, string $message)
    {
        if ($request->input('redirect_to') === 'installation') {
            return redirect()
                ->route('installations.show', $installationId)
                ->with('success', $message);
        }

        return redirect()->route('documents.index')->with('success', $message);
    }

    private function deactivatePreviousActiveVersions(array $documentData, ?DocumentInstallation $currentDocument = null): void
    {
        if (! ($documentData['est_version_active'] ?? false)) {
            return;
        }

        DocumentInstallation::query()
            ->where('installation_id', $documentData['installation_id'])
            ->where('categorie', $documentData['categorie'])
            ->where('profil_concerne', $documentData['profil_concerne'])
            ->when($currentDocument, fn ($query) => $query->whereKeyNot($currentDocument->id))
            ->update(['est_version_active' => false]);
    }
}
