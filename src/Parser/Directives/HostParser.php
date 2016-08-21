<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;

/**
 * Class HostParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class HostParser extends HostParserCore
{
    /**
     * HostParser constructor.
     *
     * @param string $base
     * @param string $effective
     */
    public function __construct($base, $effective)
    {
        parent::__construct($base, $effective);
    }

    /**
     * Client
     *
     * @return HostClient
     */
    public function client()
    {
        return new HostClient($this->base, $this->effective, isset($this->host[0]) ? [$this->host[0]] : []);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        if (isset($this->host[0])) {
            $handler->add(self::DIRECTIVE_HOST, $this->host[0]);
        }
        return true;
    }
}
