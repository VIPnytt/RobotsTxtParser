<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\InlineHostClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;

/**
 * Class InlineHostParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class InlineHostParser extends HostParserCore
{
    /**
     * InlineHostParser constructor.
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
     * @return InlineHostClient
     */
    public function client()
    {
        return new InlineHostClient($this->base, $this->effective, $this->host);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        sort($this->host);
        foreach ($this->host as $host) {
            $handler->add(self::DIRECTIVE_HOST, $host);
        }
        return true;
    }
}
