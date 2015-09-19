<?php

namespace Lighthouse\Services\Torrents\Entities;

class Error extends Base
{
    /**
     * @var string
     */
    public $message;

    /**
     * @var mixed
     */
    public $attachments;

    /**
     * @param string $message
     * @param array  $attachments
     *
     * @return Error
     */
    public static function create($message = '', $attachments = [])
    {
        $params = [
            'message'     => $message,
            'attachments' => $attachments,
        ];

        return new static($params);
    }
}
