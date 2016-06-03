<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class HostClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient
{
    /**
     * Host
     * @var string|null
     */
    private $host;

    /**
     * HostClient constructor.
     *
     * @param string|null $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * Export
     *
     * @return string|null
     */
    public function export()
    {
        return $this->host;
    }
}
