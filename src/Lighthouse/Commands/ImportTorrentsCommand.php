<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Cache;
use SplFileObject;
use Illuminate\Console\Command;
use Lighthouse\Common\Result;
use Lighthouse\Core\TorrentMapperInterface as TorrentMapper;
use Lighthouse\Core\ServiceInterface as TorrentService;
use Lighthouse\Entities\Error;
use Lighthouse\Entities\Torrent;


class ImportTorrentsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'torrents:import {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload export data from KickassTorrents';

    /**
     * Export data file.
     *
     * @var SplFileObject
     */
    protected $file;

    /**
     * @var \Lighthouse\Core\ServiceInterface
     */
    protected $service;

    /**
     * @var \Lighthouse\Core\TorrentMapperInterface
     */
    protected $mapper;

    /**
     * @var Cache
     */
    protected $cache;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TorrentService $service, TorrentMapper $mapper, Cache $cache)
    {
        parent::__construct();

        $this->service = $service;
        $this->mapper = $mapper;
        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setupCounters();

        foreach ($this->openDataFile() as $line) {

            $torrent = $this->mapTorrent($line);

            if ($this->isCached($torrent)) {
                continue;
            }

            $result = $this->service->put($torrent);

            if ($result->isSuccessful()) {
                $this->cache($torrent);
            } else {
                $this->printError($result->getError());
            }

            $this->updateCounters($result);
        }

        $this->printCounters();
    }

    /**
     * @param string $line
     * @return Torrent
     */
    private function mapTorrent($line)
    {
        try {
            return $this->mapper->map($line);
        }
        catch (\Exception $exception) {
            $this->error(
                "Mapper error on line: {$line}. Error message: {$exception->getMessage()}.");
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
            $this->error($exception->getMessage());
        }
    }

    /**
     * @return void
     */
    private function setupCounters()
    {
        $this->totalSaveCount = $this->successfulSaveCount = $this->failedSaveCount = 0;
    }

    /**
     * @param Result $result
     *
     * @return void
     */
    private function updateCounters(Result $result)
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
    private function printCounters()
    {
        $statsMessage = implode(', ', [
            "Total uploads: {$this->totalSaveCount}",
            "successful uploads: {$this->successfulSaveCount}",
            "failed uploads: {$this->failedSaveCount}",
        ]);

        $this->info($statsMessage);
    }

    private function hasTorrentChanged(Torrent $torrent)
    {
        $currentHash = $this->calculateEntityHash($torrent);
        $storedHash = $this->getStoredHash($torrent);

        return $currentHash != $storedHash;
    }

    /**
     * @param $torrent
     *
     * @return bool
     */
    private function isCached($torrent)
    {
        return is_null($torrent) or !$this->hasTorrentChanged($torrent);
    }

    private function cache(Torrent $torrent)
    {
        $entityHash = $this->calculateEntityHash($torrent);

        return $this->cache->set($torrent->hash, $entityHash);
    }

    private function calculateEntityHash(Torrent $torrent)
    {
        return md5(json_encode($torrent->toArray()));
    }

    private function getStoredHash($torrent)
    {
        return $this->redis->get($torrent->hash);
    }

    /**
     * @param FailedResult $result
     *
     * @return void
     */
    private function printError(Error $error)
    {
        if (!$this->option('verbose')) {
            return;
        }

        $message = implode(PHP_EOL,[
            "Error: {$error->message}",
            "Details: {implode('|', $error->attachments)}",
            "Line:  {trim($this->currentLine)}"
        ]);

        $this->error($message);
    }
}
