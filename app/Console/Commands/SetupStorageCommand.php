<?php

namespace App\Console\Commands;

use Elastica\Client as ElasticClient;
use Illuminate\Console\Command;
use Lighthouse\Commands\SetupElasticsearchCommand;
use Predis\Client as RedisClient;

class SetupStorageCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'storage:setup {--purge}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup storage for the Lighthouse Service';

    /**
     * @param ElasticClient $elastic
     * @param RedisClient   $redis
     */
    public function __construct(SetupElasticsearchCommand $command)
    {
        $this->command = $command;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shouldPurgeIndex = $this->option('purge');

        $this->command->handle();

        if ($shouldPurgeIndex) {
            //            $this->clearCache();
        }
    }
}
