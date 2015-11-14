<?php

namespace Lighthouse\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Lighthouse\Commands\SaveTorrent;
use Lighthouse\Services\Torrents\Common\FailedResult;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Contracts\Mapper as TorrentMapper;
use Lighthouse\Services\Torrents\Entities\Error;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Redis;
use SplFileObject;
use Symfony\Component\Console\Input\InputArgument;

class ImportTorrents extends Command
{
    use DispatchesCommands;

    /**
     * Export data file.
     *
     * @var SplFileObject
     */
    protected $file;

    /**
     * @var \Lighthouse\Services\Torrents\Contracts\Mapper
     */
    protected $mapper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'torrents:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload export data from KickassTorrents.';

    /**
     * @var
     */
    protected $currentLine = '';

    /**
     * @var int
     */
    protected $totalSaveCount = 0;

    /**
     * @var int
     */
    protected $successfulSaveCount = 0;

    /**
     * @var int
     */
    protected $failedSaveCount = 0;

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::REQUIRED, 'Path to the export file'],
        ];
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TorrentMapper $mapper)
    {
        $this->mapper = $mapper;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->setupTaskCounters();

        foreach ($this->openDataFile() as $this->currentLine) {
            $torrent = $this->mapTorrent();

            if (!$this->shouldSaveTorrent($torrent)) {
                continue;
            }

            $result = $this->saveTorrent($torrent);
            $this->handleResult($result, $torrent);
        }

        $this->printTaskCounters();
    }

    /**
     * @return Torrent
     */
    private function mapTorrent()
    {
        try {
            return $this->mapper->map($this->currentLine);
        } catch (\Exception $exception) {
            $this->error('Mapper error on line: "'.$line.'". Error message: '.$exception->getMessage().'.');
        }
    }

    /**
     * @return SplFileObject|void
     */
    private function openDataFile()
    {
        $filePath = $this->argument('path');

        try {
            return new SplFileObject($filePath, 'r');
        } catch (\RuntimeException $exception) {
            return $this->error($exception->getMessage());
        }
    }

    /**
     * @param $result
     *
     * @return void
     */
    private function handleResult(OperationResult $result, Torrent $torrent)
    {
        if ($result->isFailed() and $this->isInVerboseMode()) {
            $this->printErrorMessage($result);

            return;
        }

        $this->storeEntityHash($torrent);
        $this->updateTaskCounters($result);
    }

    /**
     * @param Error $error
     *
     * @return string
     */
    private function formatErrorMessage(Error $error)
    {
        $lines = [
            'Error: '.$error->message,
            'Details: '.implode('|', $error->attachments),
            'Line: '.trim($this->currentLine),
        ];

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param Torrent $torrent
     *
     * @return OperationResult
     */
    private function saveTorrent(Torrent $torrent)
    {
        $command = new SaveTorrent($torrent);

        return $this->dispatch($command);
    }

    /**
     * @return void
     */
    private function setupTaskCounters()
    {
        $this->totalSaveCount = $this->successfulSaveCount = $this->failedSaveCount = 0;
    }

    /**
     * @param OperationResult $result
     *
     * @return void
     */
    private function updateTaskCounters(OperationResult $result)
    {
        $this->totalSaveCount++;

        if ($result->isSuccessful()) {
            $this->successfulSaveCount++;
        } else {
            $this->failedSaveCount++;
        }
    }

    /**
     * @return void
     */
    private function printTaskCounters()
    {
        $stats = [
            'Total uploads: '.$this->totalSaveCount,
            'successful uploads: '.$this->successfulSaveCount,
            'failed uploads: '.$this->failedSaveCount,
        ];

        $statsMessage = implode(', ', $stats).'.';

        $this->info($statsMessage);
    }

    /**
     * @param FailedResult $result
     *
     * @return void
     */
    private function printErrorMessage(FailedResult $result)
    {
        $error = $result->getError();
        $message = $this->formatErrorMessage($error);
        $this->error($message);
    }

    /**
     * @return bool
     */
    private function isInVerboseMode()
    {
        return boolval($this->option('verbose'));
    }

    private function hasTorrentChanged(Torrent $torrent)
    {
        $currentHash = $this->calculateEntityHash($torrent);
        $storedHash = $this->getEntityHash($torrent);

        return $currentHash != $storedHash;
    }

    /**
     * @param $torrent
     *
     * @return bool
     */
    private function shouldSaveTorrent($torrent)
    {
        return !is_null($torrent) and $this->hasTorrentChanged($torrent);
    }

    private function storeEntityHash(Torrent $torrent)
    {
        $entityKey = $this->buildEntityKey($torrent);
        $entityHash = $this->calculateEntityHash($torrent);

        return Redis::set($entityKey, $entityHash);
    }

    private function calculateEntityHash(Torrent $torrent)
    {
        return md5(json_encode($torrent->toArray()));
    }

    private function getEntityHash($torrent)
    {
        $entityKey = $this->buildEntityKey($torrent);

        return Redis::get($entityKey);
    }

    private function buildEntityKey(Torrent $torrent)
    {
        return 'torrent:'.$torrent->hash;
    }
}
