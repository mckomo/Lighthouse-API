<?php

namespace Lighthouse\Repositories;

use Elastica\Document;
use Elastica\Exception\ConnectionException;
use Elastica\Exception\NotFoundException;
use Elastica\Query as ElasticaQuery;
use Elastica\Result;
use Elastica\Type as TorrentType;
use Lighthouse\Core\RepositoryInterface;
use Lighthouse\Exceptions\RepositoryException;
use Lighthouse\Query;
use Lighthouse\Torrent;

class ElasticsearchRepository implements RepositoryInterface
{
    /**
     * @const int
     */
    const DEFAULT_QUERY_LIMIT = 20;

    /**
     * @const array
     */
    const DEFAULT_QUERY_SORT = [
        'seedCount' => ['order' => 'desc', 'missing' => 0],
        'peerCount' => ['order' => 'desc', 'missing' => 0],
    ];

    /**
     * Endpoint to the Elastic Search Type where torrents are stored.
     *
     * @var TorrentType
     */
    private $type;

    public function __construct(TorrentType $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $infoHash
     *
     * @return Torrent|null
     */
    public function get($infoHash)
    {
        try {
            $result = $this->type->getDocument($infoHash);
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage());
        } catch (NotFoundException $exception) {
            return;
        }

        $torrent = $this->mapTorrent($result);

        return $torrent;
    }

    /**
     * @param Query $serviceQuery
     *
     * @return Torrent[]
     */
    public function search(Query $serviceQuery)
    {
        $endpointQuery = $this->buildEndpointQuery($serviceQuery);

        try {
            $results = $this->type
                ->search($endpointQuery)
                ->getResults();
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage(), 0, $exception);
        }

        $torrents = array_map(function ($result) {
            return $this->mapTorrent($result);
        }, $results);

        return $torrents;
    }

    /**
     * @param Torrent $torrent
     *
     * @return bool
     */
    public function put(Torrent $torrent)
    {
        $document = new Document($torrent->infoHash, $torrent->toArray());

        try {
            $response = $this->type->addDocument($document);
        } catch (ConnectionException $exception) {
            throw new RepositoryException($exception->getMessage(), 0, $exception);
        }

        return in_array($response->getStatus(), [200, 201]);
    }

    /**
     * @param Result $result
     *
     * @return Torrent
     */
    private function mapTorrent(Result $result)
    {
        $torrentData = $result->getData();

        return new Torrent($torrentData);
    }

    /**
     * @param Query $serviceQuery
     *
     * @return ElasticaQuery
     */
    private function buildEndpointQuery(Query $serviceQuery)
    {
        $queryCore = is_null($serviceQuery->category)
            ? $this->buildNameQuery($serviceQuery)
            : $this->buildCategoryFilteredQuery($serviceQuery);
        $queryLimit = is_null($serviceQuery->limit)
            ? static::DEFAULT_QUERY_LIMIT
            : $serviceQuery->limit;
        $querySort = is_null($serviceQuery->sortBy)
            ? static::DEFAULT_QUERY_SORT
            : $this->buildSortParameter($serviceQuery);

        $enpointQuery = new ElasticaQuery();

        return $enpointQuery
            ->setParam('query', $queryCore)
            ->setSize($queryLimit)
            ->setSort($querySort);
    }

    /**
     * @param Query $serviceQuery
     *
     * @return array
     */
    private function buildNameQuery(Query $serviceQuery)
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
     * @param Query $serviceQuery
     *
     * @return array
     */
    private function buildCategoryFilteredQuery(Query $serviceQuery)
    {
        return [
            'filtered' => [
                'query'  => $this->buildNameQuery($serviceQuery),
                'filter' => [
                    'bool' => [
                        'must' => [
                            'term' => ['category' => $serviceQuery->category],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function buildSortParameter(Query $serviceQuery)
    {
        if ($serviceQuery->sortOrder != 'asc') {
            $serviceQuery->sortOrder = 'desc';
        }

        return [
            $serviceQuery->sortBy => ['order' => $serviceQuery->sortOrder],
        ];
    }
}
