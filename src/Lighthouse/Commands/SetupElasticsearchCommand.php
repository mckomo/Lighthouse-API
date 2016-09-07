<?php

namespace Lighthouse\Commands;

use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;

class SetupElasticsearchCommand
{
    /**
     * @var Index
     */
    private $elastic;

    /**
     * @var string
     */
    private $options;

    /**
     * @param Index $elastic
     */
    public function __construct(Index $index, Type $type)
    {
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->setupLighthouseIndex();
        $this->setupTorrentMapping();
    }

    /**
     * @return void
     */
    private function setupLighthouseIndex()
    {
        $this->index->create([
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
        ]);
    }

    /**
     * @param Type $type
     *
     * @return void
     */
    private function setupTorrentMapping()
    {
        $mapping = new Mapping();

        $mapping
            ->setType($this->type)
            ->setProperties([
                'hash' => [
                    'type' => 'string', 'index' => 'no',
                ],
                'name' => [
                    'type' => 'string', 'index' => 'analyzed', 'analyzer' => 'generic_title',
                ],
                'filename' => [
                    'type' => 'string', 'index' => 'not_analyzed',
                ],
                'category' => [
                    'type' => 'string', 'index' => 'analyzed', 'analyzer' => 'simple',
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

        $this->type->setMapping($mapping);
    }
}
