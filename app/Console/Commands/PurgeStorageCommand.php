<?php

namespace App\Console\Commands;

use Elastica\Client as ElasticClient;
use Illuminate\Console\Command;
use Lighthouse\Commands\PurgeElasticsearchCommand;
use Lighthouse\Commands\PurgeRedisCommand;
use Predis\Client as RedisClient;

class SetupStorageCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'storage:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup storage for the Lighthouse Service';

    /**
     * @var PurgeElasticsearchCommand $setupCommand
     */
    private $purgeElasticCommand;


    /**
     * @var PurgeRedisCommand $setupCommand
     */
    private $purgeRedisCommand;

    /**
     * @param PurgeElasticsearchCommand $purgeElasticCommand
     * @param PurgeRedisCommand   $purgeRedisCommand
     */
    public function __construct(PurgeElasticsearchCommand $purgeElasticCommand, PurgeRedisCommand $purgeRedisCommand)
    {
        $this->purgeElasticCommand = $purgeElasticCommand;
        $this->purgeRedisCommand = $purgeRedisCommand;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->purgeElasticCommand->handle();
        $this->purgeRedisCommand->handle();
    }
}
