<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay;

use vipnytt\RobotsTxtParser\Exceptions\OutOfSyncException;

/**
 * Interface BaseInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay
 */
interface BaseInterface extends DelayInterface
{
    const RESET_NEW_DELAY = 0;

    /**
     * BaseInterface constructor.
     *
     * @param \PDO $pdo
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     */
    public function __construct(\PDO $pdo, $baseUri, $userAgent, $delay);

    /**
     * Queue
     *
     * @return float|int
     */
    public function checkQueue();

    /**
     * Reset queue
     *
     * @param float|int $newDelay
     * @return bool
     */
    public function reset($newDelay = self::RESET_NEW_DELAY);

    /**
     * Sleep
     *
     * @return float|int
     * @throws OutOfSyncException
     */
    public function sleep();

    /**
     * Timestamp with milliseconds
     *
     * @return float|int
     * @throws OutOfSyncException
     */
    public function getTimeSleepUntil();

    /**
     * Debug - get raw data
     *
     * @return array
     */
    public function debug();
}
