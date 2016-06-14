<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class DelayCore
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
abstract class DelayCore implements DelayInterface
{
    /**
     * Base Uri
     * @var string
     */
    protected $base;

    /**
     * User-agent
     * @var string
     */
    protected $userAgent;

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
}
