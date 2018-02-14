<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache;

/**
 * Class ManageCore
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/Cache.md for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Cache
 */
abstract class ManageCore implements ManageInterface
{
    /**
     * Database handler
     * @var \PDO
     */
    protected $pdo;

    /**
     * cURL options array
     * @var array
     */
    protected $curlOptions = [];

    /**
     * robots.txt size limit in byte
     * @var int
     */
    protected $byteLimit = self::BYTE_LIMIT;

    /**
     * ManageCore constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set byte limit
     *
     * @param int|null $bytes
     * @return bool
     */
    public function setByteLimit($bytes = self::BYTE_LIMIT)
    {
        $this->byteLimit = $bytes;
        return true;
    }

    /**
     * Set cURL options
     *
     * @param array $options
     * @return bool
     */
    public function setCurlOptions(array $options = self::CURL_OPTIONS)
    {
        $this->curlOptions = $options;
        return true;
    }
}
