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
        $index->create([
            'analysis' => [
                'filter' => [
                   'articles_stop' => [
                       'type'      => 'stop',
                       'stopwords' => ['a', 'an', 'the'],
                   ],
                ],
                'analyzer' => [
                    'generic_title' => [
                        'tokenizer' => 'standard',
                        'filter'    => [
                            'lowercase',
                            'asciifolding',
                            'word_delimiter',
                            'articles_stop',
                        ],
                    ],
                ],
            ],
        ], $shouldPurgeIndex);

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
                'type' => 'string', 'index' => 'no',
            ],
            'name' => [
                'type' => 'string', 'index' => 'analyzed', 'analyzer' => 'generic_title',
            ],
            'filename' => [
                'type' => 'string', 'index' => 'no',
            ],
            'category' => [
                'type' => 'string', 'index' => 'not_analyzed',
            ],
            'peerCount' => [
                'type' => 'integer', 'index' => 'not_analyzed',
            ],
            'seedCount' => [
                'type' => 'integer', 'index' => 'not_analyzed',
            ],
            'size' => [
                'type' => 'long', 'index' => 'not_analyzed',
            ],
            'uploadedAt' => [
                'type' => 'date', 'index' => 'not_analyzed',
            ],
            'url' => [
                'type' => 'string', 'index' => 'no',
            ],
        ]);

        $type->setMapping($mapping);
    }
}
