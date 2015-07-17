<?php namespace Lighthouse\Console\Commands;

use Illuminate\Console\Command;
use League\Flysystem\Exception;
use Lighthouse\Commands\UploadTorrent;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Contracts\Mapper as TorrentMapper;
use Lighthouse\Services\Torrents\Entities\Error;
use Symfony\Component\Console\Input\InputArgument;

class ImportExportData extends Command {

    use DispatchesCommands;

    /**
     * Export data file
     *
     * @var \SplFileObject
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
        $file = $this->openFile();

        foreach($file as $line)
        {
            $this->currentLine = $line;
            $torrent = $this->mapLine($line);

            if(is_null($torrent))
                continue;

            $command = new UploadTorrent($torrent);
            $result = $this->dispatch($command);

            $this->handleResult($result);
        }
	}

    /**
     * @param $line
     * @param $i
     * @return array
     */
    private function mapLine($line)
    {
        try
        {
            return $this->mapper->map($line);
        }
        catch (\Exception $exception)
        {
            $this->error('Mapper error on line: "' . $line . '". Error message: ' . $exception->getMessage() . '.');
        }
    }

    private function openFile()
    {
        try
        {
            return new \SplFileObject($this->argument('path'), 'r');
        }
        catch (\RuntimeException $exception)
        {
            return $this->error($exception->getMessage());
        }
    }

    /**
     * @param $result
     */
    private function handleResult(OperationResult $result)
    {
        if ($result->isFailed())
        {
            $error = $result->getError();
            $message = $this->formatErrorMessage($error);

            $this->error($message);
        }
    }

    private function formatErrorMessage(Error $error)
    {
        $lines = [
            'ERROR: ' . $error->message,
            'Details: ' . join('|', $error->attachments),
            'Line: ' . $this->currentLine
        ];

        return join(PHP_EOL, $lines);
    }

}
