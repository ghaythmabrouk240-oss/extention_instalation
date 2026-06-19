<?php

namespace App\Services\Graph;

use App\Models\Installation;

interface InstallationReadinessStrategy
{
    /**
     * Build the installation readiness graph.
     *
     * @param Installation $installation
     * @return array Array with keys: 'nodes', 'edges', 'summary'
     */
    public function buildGraph(Installation $installation): array;
}
