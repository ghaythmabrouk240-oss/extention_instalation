@extends('layouts.app')

@section('title', 'Budget - ' . $installation->code_installation)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Budget - {{ $installation->code_installation }}</h1>
        <p class="breadcrumb"><a href="{{ route('installations.index') }}">Installations</a> > <a href="{{ route('installations.show', $installation) }}">{{ $installation->nom }}</a> > Budget</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('installations.budget.export', $installation) }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-file-export me-2"></i>Exporter budget
        </a>
        <a href="{{ route('installations.show', $installation) }}" class="btn btn-gmao-secondary">Retour</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Table budget installation</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-gmao mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Profil</th>
                    <th>Client / site</th>
                    <th>Statut</th>
                    <th>Budget prevu</th>
                    <th>Total frais</th>
                    <th>Penalites</th>
                    <th>Total final</th>
                    <th>Devise</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $installation->code_installation }}</strong></td>
                    <td>{{ $installation->nom }}</td>
                    <td>{{ $installation->type_profil }}</td>
                    <td>{{ $installation->client?->nom ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $installation->statut }}</span></td>
                    <td>{{ number_format($summary['budget_prevu'] ?? 0, 2) }}</td>
                    <td>{{ number_format($summary['total_frais'], 2) }}</td>
                    <td>{{ number_format($summary['total_penalites'], 2) }}</td>
                    <td><strong>{{ number_format($summary['total_final'], 2) }}</strong></td>
                    <td>{{ $budget->devise ?? 'EUR' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Coverage Status Badge -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-shield-halved fa-2x me-3"></i>
        <div>
            <strong>Régime de prise en charge:</strong> 
            <span class="badge bg-{{ $summary['regime_prise_en_charge'] === 'garantie' ? 'success' : ($summary['regime_prise_en_charge'] === 'contrat_renouvelable' ? 'warning' : 'danger') }}">
                {{ $summary['regime_prise_en_charge'] === 'garantie' ? 'Sous garantie ST IET/Philips' : ($summary['regime_prise_en_charge'] === 'contrat_renouvelable' ? 'Contrat renouvelable' : 'Hors contrat') }}
            </span>
            <br>
            <strong>Statut couverture:</strong> 
            <span class="badge bg-{{ $summary['statut_couverture'] === 'garantie' ? 'success' : 'warning' }}">
                {{ $summary['statut_couverture'] === 'garantie' ? 'Garantie active' : ($summary['statut_couverture'] === 'contrat_renouvelable' ? 'Contrat renouvelable' : 'À vérifier') }}
            </span>
            @if($summary['reference_contrat'])
                <br><strong>Référence contrat:</strong> {{ $summary['reference_contrat'] }}
            @endif
        </div>
    </div>
</div>

<!-- Budget Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Budget Prévu</h6>
                <h3 class="text-primary">{{ number_format($summary['budget_prevu'] ?? 0, 2) }} {{ $budget->devise ?? 'EUR' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Frais</h6>
                <h3 class="text-info">{{ number_format($summary['total_frais'], 2) }} {{ $budget->devise ?? 'EUR' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Pénalités</h6>
                <h3 class="text-danger">{{ number_format($summary['total_penalites'], 2) }} {{ $budget->devise ?? 'EUR' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Final</h6>
                <h3 class="text-success">{{ number_format($summary['total_final'], 2) }} {{ $budget->devise ?? 'EUR' }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Budget Configuration Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Configuration Budget</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('installations.budget.update', $installation) }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Régime de prise en charge</label>
                        <select name="regime_prise_en_charge" class="form-select" required>
                            <option value="garantie" {{ $budget && $budget->regime_prise_en_charge === 'garantie' ? 'selected' : '' }}>Garantie ST IET/Philips</option>
                            <option value="contrat_renouvelable" {{ $budget && $budget->regime_prise_en_charge === 'contrat_renouvelable' ? 'selected' : '' }}>Contrat renouvelable</option>
                            <option value="hors_contrat" {{ $budget && $budget->regime_prise_en_charge === 'hors_contrat' ? 'selected' : '' }}>Hors contrat</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Budget prévu</label>
                        <input type="number" name="budget_prevu" class="form-control" step="0.01" value="{{ $budget->budget_prevu ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Devise</label>
                        <select name="devise" class="form-select" required>
                            <option value="EUR" {{ ($budget->devise ?? 'EUR') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="TND" {{ ($budget->devise ?? 'EUR') === 'TND' ? 'selected' : '' }}>TND - Dinar tunisien</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Référence contrat</label>
                        <input type="text" name="reference_contrat" class="form-control" value="{{ $budget->reference_contrat ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Statut validation</label>
                        <select name="statut_validation" class="form-select" required>
                            <option value="brouillon" {{ $budget && $budget->statut_validation === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="en_cours" {{ $budget && $budget->statut_validation === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="valide" {{ $budget && $budget->statut_validation === 'valide' ? 'selected' : '' }}>Validé</option>
                            <option value="rejete" {{ $budget && $budget->statut_validation === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label class="form-label">Notes finance</label>
                        <textarea name="notes_finance" class="form-control" rows="2">{{ $budget->notes_finance ?? '' }}</textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-gmao-primary">
                <i class="fa-solid fa-save me-1"></i> Enregistrer
            </button>
        </form>
    </div>
</div>

<!-- Time Penalty Configuration -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Pénalités de Temps</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('installations.time-penalty.update', $installation) }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Date limite contractuelle</label>
                        <input type="date" name="date_limite_contractuelle" class="form-control" value="{{ $timePenalty->date_limite_contractuelle ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Pénalité par jour</label>
                        <input type="number" name="penalite_par_jour" class="form-control" step="0.01" value="{{ $timePenalty->penalite_par_jour ?? 0 }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Payeur</label>
                        <input type="text" name="payeur" class="form-control" value="{{ $timePenalty->payeur ?? 'ST_IET' }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Raison retard</label>
                        <input type="text" name="raison_retard" class="form-control" value="{{ $timePenalty->raison_retard ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Applicable</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="applicable" class="form-check-input" value="1" {{ $timePenalty && $timePenalty->applicable ? 'checked' : '' }}>
                            <label class="form-check-label">Appliquer pénalité</label>
                        </div>
                    </div>
                </div>
            </div>
            @if($timePenalty && $timePenalty->jours_retard > 0)
            <div class="alert alert-warning">
                <strong>Jours de retard:</strong> {{ $timePenalty->jours_retard }} | 
                <strong>Montant pénalité:</strong> {{ number_format($timePenalty->montant_penalite, 2) }} {{ $budget->devise ?? 'EUR' }}
            </div>
            @endif
            <button type="submit" class="btn btn-gmao-primary">
                <i class="fa-solid fa-save me-1"></i> Enregistrer
            </button>
        </form>
    </div>
</div>

<!-- Expenses Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Dépenses</h5>
        <button type="button" class="btn btn-gmao-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fa-solid fa-plus me-1"></i> Ajouter Dépense
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-gmao table-hover">
                <thead>
                    <tr>
                        <th>Type Depense</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Fournisseur</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Montant Total</th>
                        <th>TVA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $expense->type_depense === 'transport_aller' ? 'Transport aller' : 
                                   ($expense->type_depense === 'transport_retour' ? 'Transport retour' : 
                                   ($expense->type_depense === 'hotel' ? 'Hôtel' : 
                                   ($expense->type_depense === 'repas' ? 'Repas' : 
                                   ($expense->type_depense === 'piece_equipement' ? 'Pièce équipement' : 'Autre frais')))) }}
                            </span>
                        </td>
                        <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->fournisseur ?? '-' }}</td>
                        <td>{{ $expense->quantite }}</td>
                        <td>{{ number_format($expense->montant_unitaire, 2) }}</td>
                        <td><strong>{{ number_format($expense->montant_total, 2) }}</strong></td>
                        <td>{{ number_format($expense->tva, 2) }}</td>
                        <td>
                            <form action="{{ route('installations.expenses.destroy', [$installation, $expense]) }}" method="POST" onsubmit="return confirm('Supprimer cette dépense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-action btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">Aucune dépense enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Total:</strong></td>
                        <td><strong>{{ number_format($expenses->sum('montant_total'), 2) }}</strong></td>
                        <td><strong>{{ number_format($expenses->sum('tva'), 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('installations.expenses.store', $installation) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Type de dépense</label>
                        <select name="type_depense" class="form-select" required>
                            <option value="transport_aller">Transport aller</option>
                            <option value="transport_retour">Transport retour</option>
                            <option value="hotel">Hôtel</option>
                            <option value="repas">Repas</option>
                            <option value="piece_equipement">Pièce équipement</option>
                            <option value="autre_frais">Autre frais</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date_depense" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Fournisseur</label>
                        <input type="text" name="fournisseur" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantite" class="form-control" step="0.01" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Prix unitaire</label>
                                <input type="number" name="montant_unitaire" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">TVA</label>
                                <input type="number" name="tva" class="form-control" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-gmao-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
