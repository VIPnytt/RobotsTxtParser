<?php
namespace vipnytt\RobotsTxtParser\Client\Delay;

use PDO;

/**
 * Interface ManagerInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayManager.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay
 */
interface ManagerInterface
{
    /**
     * Manager constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo);

    /**
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 60);

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopWaitTimes($limit = 100, $min = 0);
}
