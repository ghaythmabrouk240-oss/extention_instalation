<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$installation = \App\Models\Installation::with('profilCatLab', 'equipementPrincipal', 'documents')->find(1);
$strategy = \App\Services\Graph\ReadinessStrategyFactory::make('CATHETERISME');
$graph = $strategy->buildGraph($installation);

echo json_encode($graph, JSON_PRETTY_PRINT);
