<?php

namespace Lighthouse\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Lighthouse\Http\Controllers\Controller;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Common\ResultCodes;
use Lighthouse\Services\Torrents\Contracts\Service;
use Lighthouse\Services\Torrents\Entities\Error;
use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Symfony\Component\HttpFoundation\Response;

class Torrents extends Controller
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $query = $this->buildQuery($request->input());
        $operationResult = $this->service->search($query);

        return $this->prepareResponse($operationResult);
    }

    /**
     * @param string $torrentHash
     *
     * @return Response
     */
    public function get($torrentHash)
    {
        $operationResult = $this->service->get($torrentHash);

        return $this->prepareResponse($operationResult);
    }

    private function prepareResponse(OperationResult $result)
    {
        $code = $this->mapToHttpCode($result->getCode());
        $body = $result->isSuccessful()
            ? $this->prepareTorrents($result->getData())
            : $this->prepareErrorMessage($result->getError());

        return response($body, $code);
    }

    /**
     * @param array|Torrent $data
     *
     * @return array|Torrent
     */
    private function prepareTorrents($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'prepareTorrent'], $data);
        }

        return $this->prepareTorrent($data);
    }

    /**
     * @param Torrent $torrent
     *
     * @return array
     */
    private function prepareTorrent($torrent)
    {
        return $torrent->toArray();
    }

    /**
     * @param int $resultCode
     *
     * @return int
     */
    private function mapToHttpCode($resultCode)
    {
        switch ($resultCode) {
            case ResultCodes::Successful:
                return 200;
            case ResultCodes::ResourceCreated:
                return 201;
            case ResultCodes::InvalidInput:
                return 400;
            case ResultCodes::ResourceNotFound:
                return 404;
            case ResultCodes::ServiceError:
                return 500;
            default:
                throw new \InvalidArgumentException('Service returned unsupported result code.');
        }
    }

    /**
     * @param array $input
     *
     * @return ServiceQuery
     */
    private function buildQuery($input)
    {
        $params = [
            'phrase' => array_key_exists('q', $input) ? $input['q'] : '',
        ];

        if (array_key_exists('size', $input)) {
            $params['size'] = intval($input['size']);
        }

        if (array_key_exists('category', $input)) {
            $params['category'] = strtolower($input['category']);
        }

        if (array_key_exists('sort_by', $input)) {
            $params['sortBy'] = $input['sort_by'];
        }

        if (array_key_exists('sort_order', $input)) {
            $params['sortOrder'] = $input['sort_order'];
        }

        return new ServiceQuery($params);
    }

    /**
     * @param $error
     *
     * @return array
     */
    private function prepareErrorMessage(Error $error)
    {
        return ['error' => $error->toArray()];
    }
}
