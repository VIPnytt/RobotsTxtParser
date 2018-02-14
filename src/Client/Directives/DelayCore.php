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
    protected $product;

    /**
     * DelayClient constructor.
     *
     * @param string $baseUri
     * @param string $product
     */
    public function __construct($baseUri, $product)
    {
        $this->base = $baseUri;
        $this->product = $product;
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
        return $this->product;
    }

    /**
     * Handle delay
     *
     * @param Delay\ManageInterface $handler
     * @return Delay\BaseInterface
     */
    public function handle(Delay\ManageInterface $handler)
    {
        return $handler->base($this->base, $this->product, $this->getValue());
    }
}
