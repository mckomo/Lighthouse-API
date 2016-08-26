<?php

require_once 'helper.php';

function benchmarkSearch()
{
    execute('screen -d -m php artisan serve -q --port 9080');
    sleep(1);

    $searchTime = measureExecution('curl "http://localhost:9080/api/v1/torrents/?q=windows" 2> /dev/null');

    execute('pgrep -f "php -S localhost:9080" | xargs kill');

    return $searchTime;
}

if (isExecutedDirectly(__FILE__)) {
    echo benchmarkSearch();
}
