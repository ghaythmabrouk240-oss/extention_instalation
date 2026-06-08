@extends('layouts.app')

@section('title', 'Détail Installation: ' . $installation->code_installation)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Détail: {{ $installation->nom }} ({{ $installation->code_installation }})</h1>
        <p class="breadcrumb"><a href="{{ route('installations.index') }}">Installations</a> > {{ $installation->code_installation }}</p>
    </div>
    <div>
        <a href="{{ route('installations.edit', $installation) }}" class="btn btn-gmao-warning me-2">
            <i class="fa-solid fa-pen me-2"></i>Modifier
        </a>
        <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-tag"></i></div>
            <div class="stat-value">{{ $installation->type_profil }}</div>
            <div class="stat-label">Type Profil</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-signal"></i></div>
            <div class="stat-value">{{ $installation->statut }}</div>
            <div class="stat-label">Statut Actuel</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-microchip"></i></div>
            <div class="stat-value">{{ $installation->equipements->count() + ($installation->equipement_principal_id ? 1 : 0) }}</div>
            <div class="stat-label">Équipements Liés</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-file-pdf"></i></div>
            <div class="stat-value">{{ $installation->documents->count() }}</div>
            <div class="stat-label">Documents</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-3" id="installationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">Général</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab">Profil Spécifique</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="equipements-tab" data-bs-toggle="tab" data-bs-target="#equipements" type="button" role="tab">Équipements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">Historique</button>
            </li>
        </ul>
        
        <div class="tab-content p-4" id="installationTabsContent">
            <!-- GENERAL TAB -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary"><i class="fa-solid fa-info-circle me-2"></i>Identité</h4>
                        <ul class="detail-list">
                            <li><span class="detail-label">Code Installation</span><span class="detail-value">{{ $installation->code_installation }}</span></li>
                            <li><span class="detail-label">Nom</span><span class="detail-value">{{ $installation->nom }}</span></li>
                            <li><span class="detail-label">Type Profil</span><span class="detail-value">{{ $installation->type_profil }}</span></li>
                            <li><span class="detail-label">Criticité</span><span class="detail-value">{{ $installation->criticite ?? 'Non défini' }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary"><i class="fa-solid fa-network-wired me-2"></i>Rattachement</h4>
                        <ul class="detail-list">
                            <li><span class="detail-label">Client ID</span><span class="detail-value">{{ $installation->client_id ?? 'Non défini' }}</span></li>
                            <li><span class="detail-label">Équipement Principal ID</span><span class="detail-value">{{ $installation->equipement_principal_id ?? 'Non défini' }}</span></li>
                            <li><span class="detail-label">Propriétaire Interne ID</span><span class="detail-value">{{ $installation->proprietaire_interne_id ?? 'Non défini' }}</span></li>
                            <li><span class="detail-label">Créé le</span><span class="detail-value">{{ $installation->created_at->format('d/m/Y H:i') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- PROFIL TAB -->
            <div class="tab-pane fade" id="profil" role="tabpanel">
                @if($installation->type_profil === 'CATHETERISME' && $installation->profilCatLab)
                    <h4 class="mb-3 text-primary">Détails Salle de Cathétérisme</h4>
                    <ul class="detail-list">
                        <li><span class="detail-label">Système Angiographie</span><span class="detail-value">{{ $installation->profilCatLab->systeme_angiographie }}</span></li>
                        <li><span class="detail-label">Radioprotection</span><span class="detail-value">{{ $installation->profilCatLab->radioprotection }}</span></li>
                        <li><span class="detail-label">Injecteur</span><span class="detail-value">{{ $installation->profilCatLab->injecteur }}</span></li>
                        <li><span class="detail-label">Moniteurs</span><span class="detail-value">{{ $installation->profilCatLab->moniteurs }}</span></li>
                        <li><span class="detail-label">Contrôle d'accès</span><span class="detail-value">{{ $installation->profilCatLab->controle_acces ? 'Oui' : 'Non' }}</span></li>
                        <li><span class="detail-label">Table Patient</span><span class="detail-value">{{ $installation->profilCatLab->table_patient }}</span></li>
                    </ul>
                @else
                    <div class="alert alert-info">
                        Profil {{ $installation->type_profil }} non configuré ou détails non disponibles pour cette installation.
                    </div>
                @endif
            </div>

            <!-- EQUIPEMENTS TAB -->
            <div class="tab-pane fade" id="equipements" role="tabpanel">
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Désignation</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->equipements as $equipement)
                        <tr>
                            <td>{{ $equipement->code }}</td>
                            <td>{{ $equipement->designation }}</td>
                            <td>{{ $equipement->marque }}</td>
                            <td>{{ $equipement->modele }}</td>
                            <td><span class="badge bg-info">{{ $equipement->pivot->role }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun équipement secondaire lié.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- DOCUMENTS TAB -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Version</th>
                            <th>Statut</th>
                            <th>Est Bloquant</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->documents as $doc)
                        <tr>
                            <td><strong>{{ $doc->categorie }}</strong></td>
                            <td>v{{ $doc->version }}</td>
                            <td>{{ $doc->statut }}</td>
                            <td>
                                @if($doc->est_bloquant)
                                    <span class="badge bg-danger">Oui</span>
                                @else
                                    <span class="badge bg-success">Non</span>
                                @endif
                            </td>
                            <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun document rattaché.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- HISTORIQUE TAB -->
            <div class="tab-pane fade" id="historique" role="tabpanel">
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur ID</th>
                            <th>Ancien Statut</th>
                            <th>Nouveau Statut</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->historiqueStatuts as $hist)
                        <tr>
                            <td>{{ $hist->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $hist->user_id }}</td>
                            <td>{{ $hist->ancien_statut ?: '-' }}</td>
                            <td><strong>{{ $hist->nouveau_statut }}</strong></td>
                            <td>{{ $hist->commentaire }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun historique disponible.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
@endsection
