<?php namespace Lighthouse\Services\Torrents\Entities;


class Error extends Base
{
    /**
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public $attachments;

    /**
     * @param int $code
     * @param string $message
     * @param array $attachments
     * @return Error
     */
    static function create($message = '', array $attachments = null)
    {
        if (is_null($attachments))
            $attachments = [];

        $params = [
            'message' => $message,
            'attachments' => $attachments
        ];

        return new static($params);
    }

}