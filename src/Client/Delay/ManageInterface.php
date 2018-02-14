<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay;

/**
 * Interface ManageInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayInterface.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay
 */
interface ManageInterface extends DelayInterface
{
    /**
     * Top X waiting time list, max count to return
     */
    const TOP_X_LIMIT = 10;
    /**
     * Top X waiting time list, minimum delay
     */
    const TOP_X_MIN_DELAY = 0;

    /**
     * ManageInterface constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo);

    /**
     * Clean the delay table
     *
     * @return bool
     */
    public function clean();

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $minDelay
     * @return array
     */
    public function getTopWaitTimes($limit = self::TOP_X_LIMIT, $minDelay = self::TOP_X_MIN_DELAY);

    /**
     * Base class
     *
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     * @return BaseInterface
     */
    public function base($baseUri, $userAgent, $delay);
}
