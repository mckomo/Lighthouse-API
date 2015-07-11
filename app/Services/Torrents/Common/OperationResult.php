<?php

namespace Lighthouse\Services\Torrents\Common;

abstract class OperationResult {

    /**
     * @var int
     */
    protected $code;

    /**
     * @param int $code
     */
    protected function __construct($code)
    {
        $this->code = $code;
    }

    static function successful($data = null)
    {
        return new SuccessfulResult(ResultCodes::Successful);
    }

    static function failed()
    {
        return new FailedResult(ResultCodes::Failed);
    }

    public function withCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    abstract public function isSuccessful();

    /**
     * @return bool
     */
    public function isFailed()
    {
        return !$this->isSuccessful();
    }

}