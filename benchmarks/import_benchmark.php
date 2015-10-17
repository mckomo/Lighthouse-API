<?php

$start = microtime(true);

exec('php artisan elasticsearch:setup --purge');
exec('php artisan torrent:import benchmarks/assets/trimdump.txt');

$end = (microtime(true) - $start);
echo "$end";