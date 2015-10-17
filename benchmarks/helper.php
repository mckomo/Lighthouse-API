<?php

function execute($command)
{
    exec($command, $outputMessages, $resultCode);

    if ($resultCode != 0) {
        exit(join("\n", $outputMessages));
    }
}

function measureExecution($command)
{
    $start = microtime(true);
    execute($command);

    return microtime(true) - $start;
}

function isExecutedDirectly($scriptPath)
{
    return basename($_SERVER["SCRIPT_FILENAME"]) == basename($scriptPath);
}