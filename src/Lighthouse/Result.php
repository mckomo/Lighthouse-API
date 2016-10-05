<?php

namespace Lighthouse;

use Lighthouse\Core\Entity;

class Result
{
    /**
     * @var mixed
     */
    protected $code;

    /**
     * @param string            $code
     * @param Entity|array|null $data
     * @param Error|null        $error
     */
    public function __construct($code, $data = null, $error = null)
    {
        $this->code = $code;
        $this->data = $data;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array|Entity|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return is_null($this->getError());
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return !$this->isSuccessful();
    }
}
