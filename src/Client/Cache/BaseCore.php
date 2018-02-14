<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache;

use vipnytt\RobotsTxtParser\Handler\DatabaseTrait;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class BaseCore
 *
 * @package vipnytt\RobotsTxtParser\Client\Cache
 */
abstract class BaseCore implements BaseInterface
{
    use DatabaseTrait;

    /**
     * Database handler
     * @var \PDO
     */
    protected $pdo;

    /**
     * Base uri
     * @var string
     */
    protected $base;

    /**
     * cURL options
     * @var array
     */
    protected $curlOptions;

    /**
     * Byte limit
     * @var int|null
     */
    protected $byteLimit;

    /**
     * ClientCore constructor.
     *
     * @param \PDO $pdo
     * @param string $baseUri
     * @param array $curlOptions
     * @param int|null $byteLimit
     */
    public function __construct(\PDO $pdo, $baseUri, array $curlOptions, $byteLimit)
    {
        $uriParser = new UriParser($baseUri);
        $this->base = $uriParser->base();
        $this->pdo = $pdo;
        $this->curlOptions = $curlOptions;
        $this->byteLimit = $byteLimit;

    }
}
