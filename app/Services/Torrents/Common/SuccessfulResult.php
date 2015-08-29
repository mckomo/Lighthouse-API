<?php

namespace Lighthouse\Services\Torrents\Common;

class SuccessfulResult extends OperationResult
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    protected function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return true;
    }

    /**
     * @param Error $error
     */
    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
