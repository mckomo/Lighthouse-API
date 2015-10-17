<?php

require_once 'helper.php';

function benchmarkImport()
{
    return measureExecution('php artisan torrent:import benchmarks/assets/torrents_dump.csv');
}

if (isExecutedDirectly(__FILE__)) {
    echo benchmarkImport();
}