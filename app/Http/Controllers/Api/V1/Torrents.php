<?php

namespace Lighthouse\Http\Controllers\Api\V1;

use Log;
use Lighthouse\Services\Torrents\Entities\Error;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Lighthouse\Http\Controllers\Controller;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Common\ResultCodes;
use Lighthouse\Services\Torrents\Contracts\Service;
use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Symfony\Component\HttpFoundation\Response;

class Torrents extends Controller
{
    protected $service;

    public function __construct(Service $service, HttpClient $http)
    {
        $this->service = $service;
        $this->http = $http;
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
     * @param string $hash
     *
     * @return Response
     */
    public function getEntity($hash)
    {
        $operationResult = $this->service->get($hash);

        return $this->prepareResponse($operationResult);
    }

    /**
     * @param string $hash
     *
     * @return Response
     */
    public function getFile($hash)
    {
        $operationResult = $this->service->get($hash);

        if ($operationResult->isFailed()) {
            return $this->prepareResponse($operationResult);
        }

        $torrent = $operationResult->getData();
        $torrentContents = $this->download($torrent);

        if (is_null($torrentContents)) {
            return response('Torrent file is gone. Use the magnet link instead', 410);
        }

        return response($torrentContents)
            ->header('Content-Type', 'application/x-bittorrent')
            ->header('Content-Disposition', 'attachment; filename="'.$torrent->filename.'"');
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
        return $this
            ->replaceUrl($torrent)
            ->toArray();
    }

    /**
     * @param Torrent $torrent
     *
     * @return Torrent
     */
    private function replaceUrl(Torrent $torrent)
    {
        $torrent->url = route('torrent/file', ['hash' => $torrent->hash]);

        return $torrent;
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
            case ResultCodes::ResourceCreated;
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

        return new ServiceQuery($params);
    }

    /**
     * @param Torrent $torrent
     *
     * @return string
     */
    private function download(Torrent $torrent)
    {
        try {
            return $this->downloadContents($torrent->url);
        } catch (ClientException $exception) {
            Log::error($exception->getMessage());
        }
    }

    /**
     * @param $torrentUrl
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    private function downloadContents($url)
    {
        return $this->http
            ->get($url, ['headers' => ['Accept-Encoding' => 'gzip'], 'decode_content' => true])
            ->getBody();
    }

    /**
     * @param $error
     * @return array
     */
    private function prepareErrorMessage(Error $error)
    {
        return ['error' => $error->toArray()];
    }
}
