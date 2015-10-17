<?php

namespace Lighthouse\Services\Torrents\Repositories;

use Elastica\Document;
use Elastica\Exception\ConnectionException;
use Elastica\Exception\NotFoundException;
use Elastica\Query as ElasticaQuery;
use Elastica\Result;
use Elastica\Type as TorrentType;
use Lighthouse\Services\Torrents\Contracts\Repository;
use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Lighthouse\Services\Torrents\Exceptions\RepositoryException;
use Lighthouse\Services\Torrents\Mappers\ElasticSearchResult as Mapper;

class ElasticSearch implements Repository
{
    const DEFAULT_QUERY_SIZE = 20;
    const DEFAULT_QUERY_SORT = [
        'seedCount' => ['order' => 'desc', 'missing' => 0],
        'peerCount' => ['order' => 'desc', 'missing' => 0],
    ];

    /**
     * Endpoint to the Elastic Search Type where torrents are stored.
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
     *
     * @return Torrent|null
     */
    public function get($hash)
    {
        try {
            $result = $this->endpoint->getDocument($hash);
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage());
        } catch (NotFoundException $exception) {
            return;
        }

        return $this->torrentMapper->map($result);
    }

    /**
     * @param ServiceQuery $serviceQuery
     *
     * @return Torrent[]
     */
    public function search(ServiceQuery $serviceQuery)
    {
        $endpointQuery = $this->buildEndpointQuery($serviceQuery);

        try {
            $results = $this->endpoint
                ->search($endpointQuery)
                ->getResults();
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage(), 0, $exception);
        }

        return $this->mapTorrents($results);
    }

    /**
     * @param Torrent $torrent
     *
     * @return bool
     */
    public function save(Torrent $torrent)
    {
        $document = new Document($torrent->hash, $torrent->toArray());

        try {
            $response = $this->endpoint->addDocument($document);
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage(), 0, $exception);
        }

        return in_array($response->getStatus(), [200, 201]);
    }

    /**
     * @param ServiceQuery $serviceQuery
     *
     * @return ElasticaQuery
     */
    private function buildEndpointQuery(ServiceQuery $serviceQuery)
    {
        $queryCore = is_null($serviceQuery->category)
            ? $this->buildNameQuery($serviceQuery)
            : $this->buildCategoryFilteredQuery($serviceQuery);
        $querySize = is_null($serviceQuery->category)
            ? static::DEFAULT_QUERY_SIZE
            : $serviceQuery->size;
        $querySort = is_null($serviceQuery->sortBy)
            ? static::DEFAULT_QUERY_SORT
            : $this->buildSortParameter($serviceQuery);

        $enpointQuery = new ElasticaQuery();

        return $enpointQuery
            ->setParam('query', $queryCore)
            ->setSize($querySize)
            ->setSort($querySort);
    }

    /**
     * @param ServiceQuery $serviceQuery
     *
     * @return array
     */
    private function buildNameQuery($serviceQuery)
    {
        return [
            'match' => [
                'name' => [
                    'query'    => $serviceQuery->phrase,
                    'operator' => 'and',
                ],
            ],
        ];
    }

    /**
     * @param ServiceQuery $serviceQuery
     *
     * @return array
     */
    private function buildCategoryFilteredQuery($serviceQuery)
    {
        return [
            'filtered' => [
                'query'  => $this->buildNameQuery($serviceQuery),
                'filter' => [
                    'bool' => [
                        'must' => [
                            'term' => ['category' => $serviceQuery->category]
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $results
     *
     * @return Torrent[]
     */
    private function mapTorrents(array $results = [])
    {
        return array_map(function (Result $result) {
            return $this->torrentMapper->map($result);
        }, $results);
    }

    private function buildSortParameter(ServiceQuery $serviceQuery)
    {
        if ($serviceQuery->sortOrder != 'asc') {
            $serviceQuery->sortOrder = 'desc';
        }

        return [
            $serviceQuery->sortBy => ['order' => $serviceQuery->sortOrder],
        ];
    }
}
