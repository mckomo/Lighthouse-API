<?php

namespace Lighthouse\Services\Torrents;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Common\ResultCodes;
use Lighthouse\Services\Torrents\Common\Utils\ValidationHelper;
use Lighthouse\Services\Torrents\Contracts\Repository;
use Lighthouse\Services\Torrents\Contracts\Service as ServiceInterface;
use Lighthouse\Services\Torrents\Entities\Error;
use Lighthouse\Services\Torrents\Entities\ServiceQuery;
use Lighthouse\Services\Torrents\Entities\Torrent;
use Lighthouse\Services\Torrents\Exceptions\RepositoryException;
use Lighthouse\Services\Torrents\Validation\Validators\Torrent as TorrentValidator;
use Lighthouse\Services\Torrents\Validation\Validators\ServiceQuery as ServiceQueryValidator;

class Service implements ServiceInterface
{
    /**
     * @var Validator
     */
    private $repository;

    /**
     * @var Validator
     */
    private $torrentValidator;

    /**
     * @var Validator
     */
    private $queryValidator;

    public function __construct(Repository $repository,
                                TorrentValidator $torrentValidator,
                                ServiceQueryValidator $queryValidator)
    {
        $this->repository = $repository;
        $this->torrentValidator = $torrentValidator;
        $this->queryValidator = $queryValidator;
    }

    /**
     * @param $phrase
     * @param array $options
     *
     * @return OperationResult
     */
    public function search(ServiceQuery $query)
    {
        $isValid = $this->queryValidator->isValid($query, $errors);

        if (!$isValid) {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::ValidationError, $errors);

            return $this->fail($code, $error);
        }

        try {
            $torrents = $this->repository->search($query);
        } catch (RepositoryException $exception) {
            return $this->handleRepositoryException($exception);
        }

        return $this->success($torrents);
    }

    /**
     * @param Torrent $torrent
     *
     * @return OperationResult
     */
    public function save(Torrent $torrent)
    {
        $isValid = $this->torrentValidator->isValid($torrent, $validationMessages);

        if (!$isValid) {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::ValidationError, $validationMessages);

            return $this->fail($code, $error);
        }

        try {
            $this->repository->save($torrent);
        } catch (RepositoryException $exception) {
            return $this->handleRepositoryException($exception);
        }

        return $this->success();
    }

    /**
     * @param string $hash
     *
     * @return OperationResult
     */
    public function get($hash)
    {
        $torrent = null;

        if (!ValidationHelper::isHash($hash)) {
            $code = ResultCodes::InvalidInput;
            $error = Error::create(ErrorMessages::InvalidHash);

            return $this->fail($code, $error);
        }

        try {
            $torrent = $this->repository->get($hash);
        } catch (RepositoryException $exception) {
            return $this->handleRepositoryException($exception);
        }

        if (is_null($torrent)) {
            $error = Error::create(ErrorMessages::TorrentNotFound);
            $code = ResultCodes::ResourceNotFound;

            return $this->fail($code, $error);
        }

        return $this->success($torrent);
    }

    /**
     * @param int   $code
     * @param Error $error
     *
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
     *
     * @return OperationResult
     */
    private function success($data = [])
    {
        return OperationResult::successful()
            ->withData($data);
    }

    private function handleRepositoryException(RepositoryException $exception)
    {
        $code = ResultCodes::InvalidInput;
        $error = Error::create($exception->getMessage(), $exception->getPrevious());

        return $this->fail($code, $error);
    }
}
