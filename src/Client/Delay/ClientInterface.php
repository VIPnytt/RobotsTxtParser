<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay;

use PDO;

/**
 * Interface ClientInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay
 */
interface ClientInterface
{
    /**
     * Client constructor.
     *
     * @param PDO $pdo
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     */
    public function __construct(PDO $pdo, $baseUri, $userAgent, $delay);

    /**
     * Queue
     *
     * @return float|int
     */
    public function checkQueue();

    /**
     * Reset queue
     *
     * @param float|int|null $delay
     * @return bool
     */
    public function reset($delay = null);

    /**
     * Sleep
     *
     * @return float|int
     */
    public function sleep();

    /**
     * Timestamp with milliseconds
     *
     * @return float|int
     */
    public function getTimeSleepUntil();
}
