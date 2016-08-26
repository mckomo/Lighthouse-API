<?php

namespace App\Console\Commands;

use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use Elastica\Client as ElasticClient;
use Predis\Client as RedisClient;
use Illuminate\Console\Command;


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
     * @param RedisClient $redis
     */
    public function __construct()
    {
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

        if ($shouldPurgeIndex) {
            $this->clearCache();
        }
    }
}
