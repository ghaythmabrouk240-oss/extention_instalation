<?php

namespace App\Services\Graph;

use App\Models\Installation;

class MriReadinessStrategy implements InstallationReadinessStrategy
{
    public function buildGraph(Installation $installation): array
    {
        // TODO(Person A — implémenter la taxonomie IRM : champ magnétique, blindage RF, zone contrôlée, confinement ferromagnétique)
        // This stub returns a valid but empty structure to avoid breaking the endpoint
        return [
            'nodes' => [],
            'edges' => [],
            'summary' => [
                'installation' => $installation->code_installation,
                'profile' => 'IRM',
                'total_nodes' => 0,
                'blockers' => 0,
                'warnings' => 0,
                'completion_rate' => 0,
            ],
        ];
    }
}
