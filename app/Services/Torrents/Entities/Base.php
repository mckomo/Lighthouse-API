<?php namespace Lighthouse\Services\Torrents\Entities;

abstract class Base
{
    public function __construct(array $params = [])
    {
        if (count($params) > 0)
            $this->setupWith($params);
    }

    /**
     * @param $data
     * @return Base
     */
    private function setupWith($params)
    {
        foreach($params as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->$key = $value;
            }
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}