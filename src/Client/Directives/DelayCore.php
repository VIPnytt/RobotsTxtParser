<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay;
use vipnytt\RobotsTxtParser\Handler\DatabaseHandler;

/**
 * Class DelayCore
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
abstract class DelayCore implements DelayInterface, ClientInterface
{
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
     * Handler
     * @var Delay\ClientInterface
     */
    private $handler;

    /**
     * DelayClient constructor.
     *
     * @param string $baseUri
     * @param string $userAgent
     */
    public function __construct($baseUri, $userAgent)
    {
        $this->base = $baseUri;
        $this->userAgent = $userAgent;
    }

    /**
     * Get base uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->base;
    }

    /**
     * Get User-agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Handle delay
     *
     * @param PDO $pdo
     * @return Delay\ClientInterface
     */
    public function handle(PDO $pdo)
    {
        if (isset($this->handler)) {
            return $this->handler;
        }
        $handler = new DatabaseHandler($pdo);
        return $this->handler = $handler->delayClient($this->base, $this->userAgent, $this->getValue());
    }
}
