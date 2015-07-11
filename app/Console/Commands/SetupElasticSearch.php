<?php namespace Lighthouse\Console\Commands;

use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Elastica\Client;

class SetupElasticSearch extends Command {

	/**
	 * @var Client
	 */
	protected $client;

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

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Client $client)
	{
		parent::__construct();

        $this->client = $client;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $index = $this->createLighthouseIndex();
        $type = $this->createTorrentType($index);

        $this->setTorrentMapping($type);
	}

    private function createLighthouseIndex()
    {
        return $this->client
            ->getIndex('lighthouse')
            ->create();
    }

    private function createTorrentType(Index $index)
    {
        return $index->getType('torrent');
    }

    private function setTorrentMapping(Type $type)
    {
        $mapping = new Mapping();

        $mapping->setType($type);
        $mapping->setParam('_boost', ['name' => 'seedCount', 'null_value' => 0]);
        $mapping->setProperties([
            'hash' => [
                'type' => 'string', 'index' => 'no'],
            'name' => [
                'type' => 'string', 'index' => 'analyzed'],
            'peerCount' => [
                'type' => 'long', 'index' => 'no'],
            'seedCount' => [
                'type' => 'long', 'index' => 'no'],
            'size' => [
                'type' => 'long', 'index' => 'no'],
            'uploadAt' => [
                'type' => 'date', 'format' => 'dateOptionalTime', 'index' => 'no'],
            'url' => [
                'type' => 'string', 'index' => 'no'],
        ]);

        $type->setMapping($mapping);
    }

}
