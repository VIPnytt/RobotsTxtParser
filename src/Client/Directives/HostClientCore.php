<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class HostClientCore
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
abstract class HostClientCore implements ClientInterface
{
    /**
     * Host values
     * @var string[]
     */
    protected $host;

    /**
     * Base uri
     * @var string
     */
    protected $base;

    /**
     * Effective uri
     * @var string
     */
    protected $effective;

    /**
     * HostClient constructor.
     *
     * @param string $base
     * @param string $effective
     * @param string[] $host
     */
    public function __construct($base, $effective, array $host)
    {
        $this->base = $base;
        $this->effective = $effective;
        $this->host = $host;
    }
}
