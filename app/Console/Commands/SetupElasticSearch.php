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

    protected function getOptions()
    {
        return [
            ['delete', 'd', InputOption::VALUE_NONE, 'Whether should delete index if already exists']
        ];
    }

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
        $shouldDelete = $this->option('delete');

        $index = $this->createLighthouseIndex($shouldDelete);
        $type = $this->createTorrentType($index);

        $this->setTorrentMapping($type);
	}

    /**
     * @return Index
     */
    private function createLighthouseIndex($shouldDelete)
    {
        $index = $this->client
            ->getIndex('lighthouse');

        $index->create([], $shouldDelete);

        return $index;
    }

    /**
     * @param Index $index
     * @return Type
     */
    private function createTorrentType(Index $index)
    {
        return $index->getType('torrent');
    }

    private function setTorrentMapping(Type $type)
    {
        $mapping = new Mapping();

        $mapping->setType($type);
        $mapping->setProperties([
            'hash' => [
                'type' => 'string', 'index' => 'no'],
            'name' => [
                'type' => 'string', 'index' => 'analyzed'],
            'peerCount' => [
                'type' => 'long', 'index' => 'not_analyzed'],
            'seedCount' => [
                'type' => 'long', 'index' => 'not_analyzed'],
            'size' => [
                'type' => 'long', 'index' => 'no'],
            'uploadedAt' => [
                'type' => 'date', 'format' => 'dateOptionalTime', 'index' => 'no'],
            'url' => [
                'type' => 'string', 'index' => 'no'],
        ]);

        $type->setMapping($mapping);
    }

}
