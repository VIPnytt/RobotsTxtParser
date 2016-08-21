<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CommentClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CommentParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CommentParser implements ParserInterface, RobotsTxtInterface
{
    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * Comment array
     * @var string[]
     */
    private $comments = [];

    /**
     * Comment constructor.
     *
     * @param string $userAgent
     */
    public function __construct($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $this->comments[] = $line;
        return true;
    }

    /**
     * Client
     *
     * @return CommentClient
     */
    public function client()
    {
        return new CommentClient($this->userAgent, $this->comments);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        foreach ($this->comments as $comment) {
            $handler->add(self::DIRECTIVE_COMMENT, $comment);
        }
        return true;
    }
}
