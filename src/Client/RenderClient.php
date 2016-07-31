<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client;

use vipnytt\RobotsTxtParser\Handler\Directives\RootDirectiveHandler;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RenderClient
 *
 * @package vipnytt\RobotsTxtParser\Client
 */
class RenderClient implements RobotsTxtInterface
{
    /**
     * Handler
     * @var RootDirectiveHandler
     */
    private $root;

    /**
     * RenderClient constructor.
     *
     * @param RootDirectiveHandler $handler
     */
    public function __construct(RootDirectiveHandler $handler)
    {
        $this->root = $handler;
    }

    /**
     * Compatibility optimized robots.txt
     *
     * Differences compared to Normal:
     * - Each User-agent is listed with it's own separate rule set (even if it's equal to others)
     * - Byte consuming, may be multiple times larger (depending on the number of user-agents)
     * - Maximum compatibility, optimized for badly written 3rd party parsers with limited specification support
     *
     * @param string $lineSeparator
     * @return string
     */
    public function compatibility($lineSeparator = "\r\n")
    {
        return $this->generate(3, $lineSeparator);
    }

    /**
     * Generate an rule string array
     *
     * @param int $level
     * @param string $lineSeparator
     * @return string[]
     */
    private function generate($level, $lineSeparator)
    {
        $handler = new RenderHandler($level, $lineSeparator);
        if ($level === 3) {
            $this->root->userAgent()->render($handler);
        }
        $this->root->host()->render($handler);
        $this->root->cleanParam()->render($handler);
        $this->root->sitemap()->render($handler);
        if ($level !== 3) {
            $this->root->userAgent()->render($handler);
        }
        return $handler->generate();
    }

    /**
     * Minimal but normal looking robots.txt
     *
     * Differences compared to Compressed:
     * - The directives first character is uppercase
     * - Whitespace between the directive and it's value
     *
     * @param string $lineSeparator
     * @return string
     */
    public function minimal($lineSeparator = "\r\n")
    {

        return $this->generate(1, $lineSeparator);
    }

    /**
     * Normal looking robots.txt
     *
     * Differences compared to Compressed:
     * - Maximum human readability
     * - Easy to edit
     * - User-agent groups are separated with blank lines
     *
     * @param string $lineSeparator
     * @return string
     */
    public function normal($lineSeparator = "\r\n")
    {
        return $this->generate(2, $lineSeparator);
    }

    /**
     * Compressed robots.txt
     *
     * Generates an robots.txt compressed to a absolute minimum.
     * Best suited for parsing purposes and storage in databases.
     *
     * @param string $lineSeparator
     * @return string
     */
    public function compressed($lineSeparator = "\r\n")
    {
        return $this->generate(0, $lineSeparator);
    }
}
