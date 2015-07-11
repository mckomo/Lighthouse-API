<?php namespace Lighthouse\Services\Torrents;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Common\ResultCodes;
use Lighthouse\Services\Torrents\Contracts\Service as ServiceInterface;
use Lighthouse\Services\Torrents\Entities\Error;
use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Lighthouse\Services\Torrents\Validation\Utils\ValidationHelper;
use Lighthouse\Services\Torrents\Validation\Validators\Torrent as TorrentValidator;
use Lighthouse\Services\Torrents\Validation\Validators\Query as QueryValidator;
use Lighthouse\Services\Torrents\Contracts\Repository;

class Service implements ServiceInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var TorrentValidator
     */
    private $torrentValidator;

    public function __construct(Repository $repository, TorrentValidator $torrentValidator, QueryValidator $queryValidator)
    {
        $this->repository = $repository;
        $this->torrentValidator = $torrentValidator;
        $this->queryValidator = $queryValidator;
    }

    /**
     * @param $phrase
     * @param array $options
     * @return OperationResult
     */
    public function search(Query $query)
    {
        $isValid = $this->queryValidator->isValid($query, $errors);

        if (!$isValid)
        {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::ValidationError, $errors);

            return $this->fail($code, $error);
        }

        try
        {
            $torrents = $this->repository->search($query);

            return $this->success($torrents);
        }
        catch(\Exception $exception)
        {
            $code = ResultCodes::ServiceError;
            $error = Error::create($exception->getMessage());

            return $this->fail($code, $error);
        }
    }

    /**
     * @param Torrent $torrent
     * @return OperationResult
     */
    public function upload(Torrent $torrent)
    {
        $isValid = $this->torrentValidator->isValid($torrent, $errors);

        if (!$isValid)
        {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::ValidationError, $errors);

            return $this->fail($code, $error);
        }

        try
        {
            $this->repository->store($torrent);
        }
        catch(\Exception $exception)
        {
            $code = ResultCodes::ServiceError;
            $error = Error::create($exception->getMessage());

            return $this->fail($code, $error);
        }

        return $this->success();
    }

    /**
     * @param string $hash
     * @return OperationResult
     */
    public function get($hash)
    {
        if (!ValidationHelper::isHash($hash))
        {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::InvalidHash);

            return $this->fail($code, $error);
        }

        $torrent = $this->repository->get($hash);

        if (is_null($torrent))
        {
            $error = Error::create(ErrorMessages::TorrentNotFound);
            $code = ResultCodes::ResourceNotFound;

            return $this->fail($code, $error);
        }

        return OperationResult::successful()
            ->withData($torrent);
    }

    /**
     * @param int $code
     * @param Error $error
     * @return OperationResult
     */
    private function fail($code, $error)
    {
        return OperationResult::failed()
            ->withCode($code)
            ->withError($error);
    }

    /**
     * @param mixed|array $data
     * @return OperationResult
     */
    private function success($data = [])
    {
        return OperationResult::successful()->withData($data);
    }
}