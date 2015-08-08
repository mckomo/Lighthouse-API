<?php

namespace Lighthouse\Console\Commands;

use Elastica\Index;
use Elastica\Type;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Symfony\Component\Console\Input\InputOption;

class SetupElasticSearch extends Command
{
    use DispatchesCommands;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'elasticsearch:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup Elastic Search';

    protected function getOptions()
    {
        return [
            ['purge', 'p', InputOption::VALUE_NONE, 'Whether should purge existing index']
        ];
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $shouldPurgeIndex = $this->option('delete');
        $command = new \Lighthouse\Commands\SetupElasticSearch($shouldPurgeIndex);

        $this->dispatch($command);
	}

}
