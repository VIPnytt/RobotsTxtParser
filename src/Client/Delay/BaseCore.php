<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay;

use vipnytt\RobotsTxtParser\Exceptions\OutOfSyncException;
use vipnytt\RobotsTxtParser\Handler\DatabaseTrait;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class BaseCore
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay
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
     * User-agent
     * @var string
     */
    protected $userAgent;

    /**
     * Delay
     * @var float|int
     */
    protected $delay;

    /**
     * BaseCore constructor.
     *
     * @param \PDO $pdo
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     */
    public function __construct(\PDO $pdo, $baseUri, $userAgent, $delay)
    {
        $uriParser = new UriParser($baseUri);
        $this->base = $uriParser->base();
        $this->pdo = $pdo;
        $this->userAgent = $userAgent;
        $this->delay = $delay;
    }

    /**
     * Sleep
     *
     * @return float|int
     * @throws OutOfSyncException
     */
    public function sleep()
    {
        $start = microtime(true);
        $until = $this->getTimeSleepUntil();
        if (microtime(true) > $until) {
            return 0;
        }
        try {
            time_sleep_until($until);
        } catch (\Exception $warning) {
            // Timestamp already in the past
        }
        return microtime(true) - $start;
    }
}
