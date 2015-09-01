<?php

namespace Lighthouse\Handlers\Commands;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use Lighthouse\Commands\SetupElasticSearch;

class SetupElasticSearchHandler
{
    /**
     * @const string
     */
    const IndexName = 'lighthouse';

    /**
     * @const string
     */
    const TypeName = 'torrent';

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(SetupElasticSearch $command)
    {
        $shouldPurgeIndex = $command->shouldPurgeExistingIndex();

        $index = $this->createLighthouseIndex($shouldPurgeIndex);
        $type = $this->createTorrentType($index);

        $this->setTorrentMapping($type);
    }

    /**
     * @return Index
     */
    private function createLighthouseIndex($shouldPurgeIndex)
    {
        $index = $this->client->getIndex('lighthouse');
        $index->create([], $shouldPurgeIndex);

        return $index;
    }

    /**
     * @param Index $index
     *
     * @return Type
     */
    private function createTorrentType(Index $index)
    {
        return $index->getType('torrent');
    }

    /**
     * @param Type $type
     */
    private function setTorrentMapping(Type $type)
    {
        $mapping = new Mapping();

        $mapping->setType($type);
        $mapping->setProperties([
            'hash' => [
                'type' => 'string', 'index' => 'no', ],
            'name' => [
                'type' => 'string', 'index' => 'analyzed', ],
            'peerCount' => [
                'type' => 'long', 'index' => 'not_analyzed', ],
            'seedCount' => [
                'type' => 'long', 'index' => 'not_analyzed', ],
            'size' => [
                'type' => 'long', 'index' => 'no', ],
            'uploadedAt' => [
                'type' => 'date', 'format' => 'dateOptionalTime', 'index' => 'no', ],
            'url' => [
                'type' => 'string', 'index' => 'no', ],
        ]);

        $type->setMapping($mapping);
    }
}
