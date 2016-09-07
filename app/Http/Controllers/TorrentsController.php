<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lighthouse\Common\ResultCodes;
use Lighthouse\Core\ServiceInterface;
use Lighthouse\Error;
use Lighthouse\Query;
use Lighthouse\Result;
use Lighthouse\Torrent;
use Symfony\Component\HttpFoundation\Response;

class TorrentsController extends Controller
{
    protected $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $query = $this->buildQuery($request->input());
        $result = $this->service->search($query);

        return $this->prepareResponse($result);
    }

    private function prepareResponse(Result $result)
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
     * @return Query
     */
    private function buildQuery($input)
    {
        $params = [
            'phrase' => array_key_exists('q', $input) ? $input['q'] : '',
        ];

        if (array_key_exists('limit', $input)) {
            $params['limit'] = intval($input['limit']);
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

        return new Query($params);
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
