<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

use vipnytt\RobotsTxtParser\Exceptions\OutOfSyncException;

/**
 * Trait DatabaseTrait
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
trait DatabaseTrait
{
    /**
     * Clock sync check
     *
     * @param int $time
     * @param int $max
     * @throws OutOfSyncException
     */
    protected function clockSyncCheck($time, $max)
    {
        if (abs(time() - $time) >= $max) {
            throw new OutOfSyncException('PHP and database server clocks are out of sync. Please re-sync!');
        }
    }
}
