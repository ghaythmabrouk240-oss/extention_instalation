<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $equipement->designation }} - GMAO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .equipment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
        }
        .equipment-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .equipment-header .code {
            font-size: 1rem;
            opacity: 0.9;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 1rem;
            overflow: hidden;
        }
        .info-card .card-header {
            background-color: #667eea;
            color: white;
            padding: 1rem;
            font-weight: 600;
        }
        .info-row {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            padding: 1rem;
            flex-wrap: wrap;
        }
        .action-btn {
            flex: 1;
            min-width: 120px;
            padding: 0.75rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .installation-link {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="equipment-header">
        <h1>{{ $equipement->designation }}</h1>
        <div class="code">{{ $equipement->code }}</div>
    </div>

    <div class="info-card">
        <div class="card-header">
            <i class="fa-solid fa-info-circle me-2"></i>Informations Techniques
        </div>
        <div class="info-row">
            <span class="info-label">Numéro</span>
            <span class="info-value">{{ $equipement->numero_equipement }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Marque</span>
            <span class="info-value">{{ $equipement->marque }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Modèle</span>
            <span class="info-value">{{ $equipement->modele }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Numéro Série</span>
            <span class="info-value">{{ $equipement->numero_serie }}</span>
        </div>
        @if($equipement->date_installation)
        <div class="info-row">
            <span class="info-label">Installation</span>
            <span class="info-value">{{ $equipement->date_installation->format('d/m/Y') }}</span>
        </div>
        @endif
    </div>

    @if($equipement->installations->count() > 0)
    <div class="info-card">
        <div class="card-header">
            <i class="fa-solid fa-building me-2"></i>Installations
        </div>
        @foreach($equipement->installations as $installation)
        <div class="info-row">
            <span class="info-label">{{ $installation->code_installation }}</span>
            <span class="info-value">
                <span class="status-badge {{ $installation->statut === 'Opérationnel' ? 'status-active' : 'status-inactive' }}">
                    {{ $installation->statut }}
                </span>
            </span>
        </div>
        @endforeach
    </div>
    @endif

    @if($equipement->sousEquipements->count() > 0)
    <div class="info-card">
        <div class="card-header">
            <i class="fa-solid fa-cogs me-2"></i>Sous-équipements ({{ $equipement->sousEquipements->count() }})
        </div>
        @foreach($equipement->sousEquipements->take(5) as $sousEquipement)
        <div class="info-row">
            <span class="info-label">{{ $sousEquipement->designation }}</span>
            <span class="info-value">{{ $sousEquipement->marque }}</span>
        </div>
        @endforeach
        @if($equipement->sousEquipements->count() > 5)
        <div class="info-row">
            <span class="info-label">...</span>
            <span class="info-value">+{{ $equipement->sousEquipements->count() - 5 }} autres</span>
        </div>
        @endif
    </div>
    @endif

    <div class="action-buttons">
        @if($equipement->installations->count() > 0)
            @php
                $mainInstallation = $equipement->installations->first();
                $profile = $mainInstallation->type_profil ?? 'CATHETERISME';
            @endphp
            <a href="{{ route('installations.graph', ['installation_id' => $mainInstallation->id, 'profile' => $profile]) }}" class="action-btn installation-link">
                <i class="fa-solid fa-project-diagram"></i> Graphe
            </a>
        @endif
        <a href="{{ route('equipements.show', $equipement) }}" class="action-btn btn-primary">
            <i class="fa-solid fa-eye"></i> Détails
        </a>
        <a href="{{ route('equipements.edit', $equipement) }}" class="action-btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Modifier
        </a>
    </div>

    <div style="text-align: center; padding: 1rem; color: #999; font-size: 0.875rem;">
        <i class="fa-solid fa-qrcode"></i> Scanné via GMAO
    </div>
</body>
</html>
