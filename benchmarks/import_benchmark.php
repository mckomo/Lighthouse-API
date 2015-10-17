<?php

$start = microtime(true);

exec('php artisan elasticsearch:setup --purge');
exec('php artisan torrent:import benchmark/assets/trimdump.txt');

$end = (microtime(true) - $start);
echo "$end";