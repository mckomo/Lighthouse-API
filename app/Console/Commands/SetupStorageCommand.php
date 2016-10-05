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
    protected $signature = 'storage:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup storage for the Lighthouse Service';

    /**
     * @var SetupElasticsearchCommand
     */

    /**
     * @param ElasticClient $elastic
     * @param RedisClient   $redis
     */
    public function __construct(SetupElasticsearchCommand $setupCommand)
    {
        $this->setupCommand = $setupCommand;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setupCommand->handle();
    }
}
