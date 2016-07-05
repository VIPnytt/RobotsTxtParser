<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CommentClient;
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
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Comment array
     * @var string[]
     */
    private $comments = [];

    /**
     * Client cache
     * @var CommentClient
     */
    private $client;

    /**
     * Comment constructor.
     *
     * @param string $base
     * @param string $userAgent
     */
    public function __construct($base, $userAgent)
    {
        $this->base = $base;
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
        if (isset($this->client)) {
            return $this->client;
        }
        return $this->client = new CommentClient($this->base, $this->userAgent, $this->comments);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->comments as $comment) {
            $result[] = self::DIRECTIVE_COMMENT . ':' . $comment;
        }
        return $result;
    }
}
