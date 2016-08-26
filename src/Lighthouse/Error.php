<?php

namespace Lighthouse;

use Lighthouse\Core\Entity;

class Error extends Entity
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
     * @param array  $attachment
     *
     * @return Error
     */
    public static function create($message = '', $attachment = [])
    {
        $params = [
            'message'     => $message,
            'attachments' => $attachment,
        ];

        return new static($params);
    }
}
