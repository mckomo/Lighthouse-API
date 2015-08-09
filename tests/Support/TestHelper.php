<?php
/**
 * Created by PhpStorm.
 * User: mckomo
 * Date: 09.08.15
 * Time: 12:50
 */

namespace Lighthouse\Tests\Support;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TestHelper
{
    /**
     * @return strings
     */
    public static function purgeTorrents()
    {
        TestHelper::runCommand('php artisan elasticsearch:setup --purge');
    }

    /**
     * @return strings
     */
    public static function importSampleTorrents()
    {
        TestHelper::runCommand('php artisan torrents:import tests/Support/fixtures/exportdata.txt');
    }

    /**
     * @param string $command
     * @return string
     */
    public static function runCommand($command)
    {
        $process = new Process($command);

        try
        {
            $process->mustRun();
        }
        catch (ProcessFailedException $exception)
        {
            throw new \RuntimeException($exception->getMessage());
        }

        return $process->getOutput();
    }
}