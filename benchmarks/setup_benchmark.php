<?php

require_once 'helper.php';
require_once 'import_benchmark.php';
require_once 'search_benchmark.php';

$importTime = benchmarkImport();
$searchTime = benchmarkSearch();

echo "Import time: {$importTime}" . PHP_EOL
echo "Search time: $searchTime";
