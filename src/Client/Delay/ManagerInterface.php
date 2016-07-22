<?php
namespace vipnytt\RobotsTxtParser\Client\Delay;

use PDO;

/**
 * Interface ManagerInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayInterface.md for documentation
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
    public function clean($delay);

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopWaitTimes($limit, $min);
}
