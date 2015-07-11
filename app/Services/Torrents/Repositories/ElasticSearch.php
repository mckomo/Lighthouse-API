<?php namespace Lighthouse\Services\Torrents\Repositories;

use Elastica\Document;
use Elastica\Exception\ConnectionException;
use Elastica\Exception\NotFoundException;
use Elastica\Query as ElasticaQuery;
use Elastica\Query\QueryString;
use Elastica\Result;
use Elastica\Type as TorrentType;
use Lighthouse\Services\Torrents\Contracts\Repository;
use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Lighthouse\Services\Torrents\Exceptions\RepositoryException;
use Lighthouse\Services\Torrents\Mappers\ElasticSearchResult as Mapper;


class ElasticSearch implements Repository
{
    const DEFAULT_QUERY_SIZE = 20;
    const DEFAULT_QUERY_SORT = [
        'seedCount' => ['order' => 'desc'],
        'peerCount' => ['order' => 'desc'],
    ];

    /**
     * Endpoint to the Elastic Search Type where torrents are stored
     *
     * @var TorrentType
     */
    private $endpoint;

    public function __construct(TorrentType $endpoint, Mapper $mapper)
    {
        $this->endpoint = $endpoint;
        $this->torrentMapper = $mapper;
    }

    /**
     * @param $hash
     * @return Torrent|null
     */
    public function get($hash)
    {
        try
        {
            $result = $this->endpoint->getDocument($hash);
            $torrent = $this->torrentMapper->map($result);

            return $torrent;
        }
        catch(ConnectionException $exception)
        {
            throw new RepositoryException($exception->getMessage());
        }
        catch(NotFoundException $exception)
        {
            return null;
        }
    }

    /**
     * @param Query $query
     * @return Torrent[]
     */
    public function search(Query $query)
    {
        $endpointQuery = $this->buildEndpointQuery($query);

        try
        {
            $results = $this->endpoint
                ->search($endpointQuery)
                ->getResults();

            return $this->mapTorrents($results);
        }
        catch(ConnectionException $exception)
        {
            throw new RepositoryException($exception->getMessage());
        }
    }

    /**
     * @param Torrent $torrent
     * @return bool
     */
    public function store(Torrent $torrent)
    {
        try
        {
            $document = new Document($torrent->hash, $torrent->toArray());
            $response = $this->endpoint->addDocument($document);

            return in_array($response->getStatus(), [200, 201]);
        }
        catch(ConnectionException $exception)
        {
            throw new RepositoryException($exception->getMessage());
        }
    }

    /**
     * @param Query $query
     * @return ElasticaQuery
     */
    private function buildEndpointQuery(Query $query)
    {
        $enpointQuery = new ElasticaQuery();

        $querySize = is_null($query->category)
            ? static::DEFAULT_QUERY_SIZE
            : $query->size;
        $phraseQuery = is_null($query->category)
            ? $this->getPhraseQuery($query->phrase)
            : $this->getCategoryFilteredQuery($query->phrase, $query->category);

        $enpointQuery
            ->setParam('query', $phraseQuery)
            ->setSize($querySize)
            ->setSort(static::DEFAULT_QUERY_SORT);

        return $enpointQuery;
    }

    /**
     * @param string $phrase
     * @return array
     */
    private function getPhraseQuery($phrase)
    {
        return [
            'match' => [
                'name' => [
                    'query' => $phrase,
                    'operator' => 'and',
                ]
            ]
        ];
    }

    /**
     * @param string $phrase
     * @param string $category
     * @return array
     */
    private function getCategoryFilteredQuery($phrase, $category)
    {
        return [
            'filtered' => [
                'query' => $this->getPhraseQuery($phrase),
                'filter' => [
                    'bool' => [
                        'should' => [
                            'term' => ['category' => $category]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param array $results
     * @return Torrent[]
     */
    private function mapTorrents(array $results = [])
    {
        return array_map(function(Result $result) {
            return $this->torrentMapper->map($result);
        }, $results);
    }

}