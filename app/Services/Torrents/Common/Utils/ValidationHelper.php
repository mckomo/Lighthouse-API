<?php

namespace Lighthouse\Services\Torrents\Common\Utils;

class ValidationHelper
{
    /**
     * @param string $hashCandidate
     *
     * @return bool
     */
    public static function isHash($hashCandidate)
    {
        return preg_match('/^[a-fA-F\d]{40}$/', $hashCandidate);
    }

    /**
     * @param string $string
     * @param int    $bound
     *
     * @return bool
     */
    public static function isLongerThan($string, $bound = 0)
    {
        return !is_null($string) && strlen($string) > $bound;
    }

    /**
     * @param int $size
     *
     * @return bool
     */
    public static function isPositiveInteger($number)
    {
        return is_int($number) && $number > 0;
    }

    /**
     * @param int $number
     *
     * @return bool
     */
    public static function isNegativeInteger($number)
    {
        return is_int($number) && $number < 0;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    public static function isIso8601Utc($date)
    {
        return preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}Z$/', $date);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isValidUtf8($string)
    {
        return mb_check_encoding($string, 'UTF-8');
    }

    /**
     * @param float $number
     * @param float $lb
     * @param float $rb
     * @return bool
     */
    public static function isInRange($number, $lb, $rb)
    {
        return $number >= $lb && $number <= $rb;
    }

    /**
     * @param $link
     *
     * @return int
     */
    public static function isMagnetLink($link)
    {
        return preg_match('/^magnet:\?xt=urn:[a-z0-9]+:[a-z0-9]{32}/i', $link);
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    public static function isValidFilename($filename)
    {
        return preg_match('/^[\w\-]+(\.[\w]+)?$/', $filename);
    }
}
