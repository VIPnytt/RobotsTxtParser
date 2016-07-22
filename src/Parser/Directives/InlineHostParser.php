<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\InlineHostClient;

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
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->host as $host) {
            $result[] = self::DIRECTIVE_HOST . ':' . $host;
        }
        sort($result);
        return $result;
    }
}
