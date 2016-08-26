<?php

namespace Lighthouse;

use Lighthouse\Common\ErrorMessages;
use Lighthouse\Result;
use Lighthouse\Common\ResultCodes;
use Lighthouse\Core\ValidatorInterface;
use Lighthouse\Utils\ValidationHelper;
use Lighthouse\Core\RepositoryInterface;
use Lighthouse\Core\ServiceInterface;
use Lighthouse\Exceptions\RepositoryException;
use Lighthouse\Validators\TorrentValidator;
use Lighthouse\Validators\QueryValidator;

class Service implements ServiceInterface
{
    /**
     * @var ValidatorInterface
     */
    private $repository;

    /**
     * @var ValidatorInterface
     */
    private $torrentValidator;

    /**
     * @var ValidatorInterface
     */
    private $queryValidator;

    public function __construct(RepositoryInterface $repository,
                                TorrentValidator $torrentValidator,
                                QueryValidator $queryValidator)
    {
        $this->repository = $repository;
        $this->torrentValidator = $torrentValidator;
        $this->queryValidator = $queryValidator;
    }

    /**
     * @param string $infoHash
     *
     * @return Result
     */
    public function get($infoHash)
    {
        $torrent = null;

        if (!ValidationHelper::isHash($infoHash)) {
            return $this->invalidInput(ErrorMessages::InvalidInfoHash);
        }

        try {
            $torrent = $this->repository->get($infoHash);
        } catch (RepositoryException $exception) {
            return $this->repositoryException($exception);
        }

        if (is_null($torrent)) {
            return $this->torrentNotFound();
        }

        return $this->successful($torrent);
    }

    /**
     * @param Query $query
     * @return Result
     */
    public function search(Query $query)
    {
        $isValid = $this->queryValidator->isValid($query, $validationErrors);

        if (!$isValid) {
            return $this->invalidInput($validationErrors);
        }

        try {
            $torrents = $this->repository->search($query);
        } catch (RepositoryException $exception) {
            return $this->repositoryException($exception);
        }

        return $this->successful($torrents);
    }

    /**
     * @param Torrent $torrent
     *
     * @return Result
     */
    public function put(Torrent $torrent)
    {
        $isValid = $this->torrentValidator->isValid($torrent, $errors);

        if (!$isValid) {
            return $this->invalidInput($errors);
        }

        try {
            $this->repository->put($torrent);
        } catch (RepositoryException $exception) {
            return $this->repositoryException($exception);
        }

        return $this->resourceCreated($torrent);
    }

    /**
     * @param mixed|array $data
     * @return Result
     */
    private function successful($data = [])
    {
        return new Result(ResultCodes::Successful, $data);
    }


    /**
     * @param mixed|array $data
     * @return Result
     */
    private function resourceCreated($data = [])
    {
        return new Result(ResultCodes::ResourceCreated, $data);
    }

    /**
     * @param array $validationErrors
     * @return Result
     */
    private function invalidInput($validationErrors = [])
    {
        $error = Error::create(ErrorMessages::ValidationError, $validationErrors);

        return new Result(ResultCodes::InvalidInput, null, $error);
    }

    /**
     * @param RepositoryException $exception
     * @return Result
     */
    private function repositoryException(RepositoryException $exception)
    {
        $error = Error::create($exception->getMessage(), $exception->getPrevious());

        return new Result(ResultCodes::InvalidInput, null, $error);
    }


    private function torrentNotFound()
    {
        $error = Error::create(ErrorMessages::TorrentNotFound);

        return new Result(ResultCodes::ResourceNotFound, null, $error);
    }
}
