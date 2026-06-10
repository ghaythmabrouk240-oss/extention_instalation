@php($document = $report['document'] ?? null)

<div class="col-md-6 col-lg-4">
    <div class="card h-100 {{ $document ? 'border-success' : 'border-warning' }}">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">
                <i class="fa-solid {{ $report['icon'] }} me-2"></i>{{ $report['categorie'] }}
                @if($report['required'] ?? false)
                    <span class="badge bg-danger ms-1">Requis PRD</span>
                @endif
            </h5>
            <p class="small text-muted">{{ $report['description'] }}</p>

            @if($document)
                <p class="mb-2">
                    <span class="badge bg-success">Present</span>
                    v{{ $document->version }} - {{ $document->statut }}
                </p>
                @if($document->fichier_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($document->fichier_path) }}" target="_blank" class="btn btn-sm btn-gmao-secondary me-2 mb-2">
                        <i class="fa-solid fa-file-arrow-down me-1"></i>{{ $document->fichier_original_name ?? 'Ouvrir le fichier' }}
                    </a>
                @else
                    <p class="small text-warning mb-2">Metadonnees seulement, aucun fichier joint.</p>
                @endif
                <div class="mt-auto d-flex flex-wrap gap-2">
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-action btn-view">Detail</a>
                    <a href="{{ route('documents.create', [
                        'installation_id' => $installation->id,
                        'categorie' => $report['categorie'],
                        'type_rapport' => $report['type_rapport'],
                        'redirect_to' => 'installation',
                        'requires_file' => 1,
                    ]) }}" class="btn btn-sm btn-gmao-secondary">Nouvelle version</a>
                </div>
            @else
                <p class="mb-3"><span class="badge bg-warning text-dark">Manquant</span></p>
                <a href="{{ route('documents.create', [
                    'installation_id' => $installation->id,
                    'categorie' => $report['categorie'],
                    'type_rapport' => $report['type_rapport'],
                    'redirect_to' => 'installation',
                    'requires_file' => 1,
                ]) }}" class="btn btn-gmao-primary btn-sm mt-auto">
                    <i class="fa-solid fa-file-circle-plus me-2"></i>Televerser PDF / scan
                </a>
            @endif
        </div>
    </div>
</div>
