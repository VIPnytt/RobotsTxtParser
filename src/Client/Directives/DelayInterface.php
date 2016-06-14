<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\DelayHandlerClient;

/**
 * Interface DelayInterface
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
interface DelayInterface
{
    /**
     * Get value
     *
     * @return float|int
     */
    public function getValue();

    /**
     * Get base uri
     *
     * @return string
     */
    public function getBaseUri();

    /**
     * Get User-agent string
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Client
     *
     * @param PDO $pdo
     * @return DelayHandlerClient
     */
    public function client(PDO $pdo);
}
