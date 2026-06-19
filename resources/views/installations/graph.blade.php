@extends('layouts.app')

@section('title', 'Graphe Installation')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Graphe Installation</h1>
        <p class="breadcrumb">GMAO > Installations > Graphe</p>
    </div>
    <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Installation</label>
        <select id="installation-select" class="form-select">
            <option value="">Sélectionner une installation...</option>
            @foreach($installations as $installation)
                <option value="{{ $installation->id }}" data-profile="{{ $installation->type_profil }}" {{ $installation->id == $selectedInstallationId ? 'selected' : '' }}>
                    {{ $installation->code_installation }} - {{ $installation->nom }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Profil</label>
        <select id="profile-select" class="form-select">
            <option value="CATHETERISME" selected>Cathétérisme</option>
            <option value="IRM">IRM</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Filtre État</label>
        <select id="state-filter" class="form-select">
            <option value="all">Tous</option>
            <option value="vert">Vert</option>
            <option value="jaune">Jaune</option>
            <option value="rouge">Rouge</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Filtre Type</label>
        <select id="type-filter" class="form-select">
            <option value="all">Tous</option>
            <option value="equipement_secondaire">Equipement secondaire</option>
            <option value="sous_equipement">Sous-equipement</option>
            <option value="equipement_principal">Équipement principal</option>
            <option value="composant_profil">Composant profil</option>
            <option value="securite">Sécurité</option>
            <option value="securite_composite">Sécurité composite</option>
            <option value="composite">Composite</option>
            <option value="document">Document</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Graphe de Prêt</h5>
        <div class="d-flex gap-3">
            <div class="d-flex align-items-center">
                <span class="badge bg-success me-2">●</span>
                <small>Prêt / valide</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-warning me-2">●</span>
                <small>À vérifier / incomplet</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-danger me-2">●</span>
                <small>Manquant / bloquant</small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="graph-container" style="height: 600px; width: 100%; border: 1px solid #ddd; background-color: #f9f9f9;"></div>
        <div id="empty-state" class="text-center py-5" style="display: none;">
            <i class="fa-solid fa-network-wired fa-3x text-muted mb-3"></i>
            <p class="text-muted">Sélectionnez une installation pour afficher le graphe</p>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-md-3">
                <strong>Total noeuds:</strong> <span id="total-nodes">0</span>
            </div>
            <div class="col-md-3">
                <strong>Bloqueurs:</strong> <span id="blockers" class="text-danger">0</span>
            </div>
            <div class="col-md-3">
                <strong>Avertissements:</strong> <span id="warnings" class="text-warning">0</span>
            </div>
            <div class="col-md-3">
                <strong>Taux de complétion:</strong> <span id="completion-rate">0%</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    console.log('vis-network available:', typeof vis !== 'undefined');
    
    const container = document.getElementById('graph-container');
    const emptyState = document.getElementById('empty-state');
    const installationSelect = document.getElementById('installation-select');
    const profileSelect = document.getElementById('profile-select');
    const stateFilter = document.getElementById('state-filter');
    const typeFilter = document.getElementById('type-filter');
    
    console.log('Elements found:', {
        container: !!container,
        emptyState: !!emptyState,
        installationSelect: !!installationSelect,
        profileSelect: !!profileSelect,
        stateFilter: !!stateFilter,
        typeFilter: !!typeFilter,
    });
    
    let network = null;
    let graphData = null;
    
    function loadGraph() {
        const installationId = installationSelect.value;
        const profile = profileSelect.value;
        
        if (!installationId) {
            container.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        container.style.display = 'block';
        emptyState.style.display = 'none';
        
        fetch(`/dashboard/installation-graph?installation_id=${installationId}&profile=${profile}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Graph data received:', data);
                graphData = data;
                updateSummary(data.summary);
                renderGraph(data);
            })
            .catch(error => {
                console.error('Error loading graph:', error);
            });
    }
    
    function updateSummary(summary) {
        document.getElementById('total-nodes').textContent = summary.total_nodes;
        document.getElementById('blockers').textContent = summary.blockers;
        document.getElementById('warnings').textContent = summary.warnings;
        document.getElementById('completion-rate').textContent = summary.completion_rate + '%';
    }
    
    function renderGraph(data) {
        console.log('Rendering graph with data:', data);
        const stateFilterValue = stateFilter.value;
        const typeFilterValue = typeFilter.value;
        
        // Filter nodes
        const filteredNodes = data.nodes.filter(node => {
            if (stateFilterValue !== 'all' && node.state !== stateFilterValue) return false;
            if (typeFilterValue !== 'all' && node.type !== typeFilterValue) return false;
            return true;
        });
        
        console.log('Filtered nodes:', filteredNodes);
        
        // Get filtered node IDs
        const filteredNodeIds = new Set(filteredNodes.map(n => n.id));
        
        // Filter edges to only include those between filtered nodes
        const filteredEdges = data.edges.filter(edge => 
            filteredNodeIds.has(edge.source) && filteredNodeIds.has(edge.target)
        );
        
        console.log('Filtered edges:', filteredEdges);
        
        // Convert nodes for vis-network
        const visNodes = filteredNodes.map(node => ({
            id: node.id,
            label: node.label,
            color: getNodeColor(node.state),
            font: { size: 14 },
            title: node.tooltip,
        }));
        
        // Convert edges for vis-network
        const visEdges = filteredEdges.map(edge => ({
            from: edge.source,
            to: edge.target,
            color: getEdgeColor(edge.state, edge.blocking),
            width: edge.blocking ? 3 : 1,
            title: edge.relation,
            arrows: 'to',
        }));
        
        console.log('vis-network nodes:', visNodes);
        console.log('vis-network edges:', visEdges);
        
        const visData = {
            nodes: new vis.DataSet(visNodes),
            edges: new vis.DataSet(visEdges)
        };
        
        const options = {
            physics: {
                stabilization: true,
                barnesHut: {
                    gravitationalConstant: -2000,
                    centralGravity: 0.3,
                    springLength: 200,
                    springConstant: 0.04,
                    damping: 0.09,
                    avoidOverlap: 0.5
                }
            },
            nodes: {
                size: 25,
                shape: 'dot',
                borderWidth: 2,
                borderWidthSelected: 4
            },
            edges: {
                smooth: {
                    type: 'dynamic',
                    forceDirection: 'none'
                }
            },
            interaction: {
                hover: true,
                tooltipDelay: 200,
                zoomView: true
            }
        };
        
        console.log('Creating network with container:', container);
        
        if (network) {
            network.destroy();
        }
        
        try {
            network = new vis.Network(container, visData, options);
            console.log('Network created successfully:', network);
        } catch (error) {
            console.error('Error creating network:', error);
        }
        
        // Handle node clicks for navigation
        network.on('click', function(params) {
            if (params.nodes.length > 0) {
                const nodeId = params.nodes[0];
                const node = data.nodes.find(n => n.id === nodeId);
                if (node) {
                    handleNodeClick(node);
                }
            }
        });
    }
    
    function getNodeColor(state) {
        switch(state) {
            case 'vert': return '#28a745';
            case 'jaune': return '#ffc107';
            case 'rouge': return '#dc3545';
            default: return '#6c757d';
        }
    }
    
    function getEdgeColor(state, blocking) {
        if (blocking) return '#dc3545';
        switch(state) {
            case 'vert': return '#28a745';
            case 'jaune': return '#ffc107';
            case 'rouge': return '#dc3545';
            default: return '#6c757d';
        }
    }
    
    function handleNodeClick(node) {
        if (!node.source_id) return;
        
        switch(node.source_table) {
            case 'installations':
                window.location.href = `/installations/${node.source_id}`;
                break;
            case 'equipements':
                window.location.href = `/equipements/${node.source_id}`;
                break;
            case 'sous_equipements':
                window.location.href = `/sous-equipements/${node.source_id}`;
                break;
            case 'document_installations':
                window.location.href = `/documents/${node.source_id}`;
                break;
            default:
                console.log('Navigation not implemented for:', node.source_table);
        }
    }
    
    // Event listeners
    function syncProfileFromInstallation() {
        const selectedOption = installationSelect.options[installationSelect.selectedIndex];
        const installationProfile = selectedOption?.dataset.profile;

        if (installationProfile) {
            profileSelect.value = installationProfile;
        }
    }

    function selectInstallationForProfile(profile) {
        const matchingOption = Array.from(installationSelect.options)
            .find(option => option.value && option.dataset.profile === profile);

        installationSelect.value = matchingOption?.value || '';
    }

    installationSelect.addEventListener('change', () => {
        syncProfileFromInstallation();
        loadGraph();
    });
    profileSelect.addEventListener('change', () => {
        selectInstallationForProfile(profileSelect.value);
        loadGraph();
    });
    stateFilter.addEventListener('change', () => {
        if (graphData) renderGraph(graphData);
    });
    typeFilter.addEventListener('change', () => {
        if (graphData) renderGraph(graphData);
    });
    
    // Load initial graph if installation is selected
    console.log('Initial installation selected:', installationSelect.value);
    if (installationSelect.value) {
        console.log('Loading initial graph...');
        syncProfileFromInstallation();
        loadGraph();
    } else {
        console.log('No installation selected, showing empty state');
        container.style.display = 'none';
        emptyState.style.display = 'block';
    }
});
</script>
@endsection
