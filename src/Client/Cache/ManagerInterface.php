<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache;

use PDO;
use vipnytt\RobotsTxtParser\TxtClient;

/**
 * Interface ManagerInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/Cache.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Cache
 */
interface ManagerInterface
{
    /**
     * Manager constructor.
     *
     * @param PDO $pdo
     * @param array $curlOptions
     * @param int|null $byteLimit
     */
    public function __construct(PDO $pdo, array $curlOptions, $byteLimit);

    /**
     * Parser client
     *
     * @param string $base
     * @return TxtClient
     */
    public function client($base);

    /**
     * Invalidate cache
     *
     * @param $base
     * @return bool
     */
    public function invalidate($base);

    /**
     * Process the update queue
     *
     * @param float|int|null $timeLimit
     * @param int|null $workerID
     * @return string[]
     */
    public function cron($timeLimit, $workerID);

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay);

    /**
     * Debug - get raw data
     *
     * @param $base
     * @return array
     */
    public function debug($base);
}
