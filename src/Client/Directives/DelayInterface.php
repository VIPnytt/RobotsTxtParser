<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Client\Delay;

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
     * Handle delay
     *
     * @param Delay\ManageInterface $handler
     * @return Delay\BaseInterface
     */
    public function handle(Delay\ManageInterface $handler);
}
