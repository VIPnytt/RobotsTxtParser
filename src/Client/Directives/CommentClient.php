<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CommentClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/CommentClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CommentClient implements ClientInterface, RobotsTxtInterface
{
    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * Comments
     * @var string[]
     */
    private $comments = [];

    /**
     * CommentClient constructor.
     *
     * @param string $userAgent
     * @param array $comments
     */
    public function __construct($userAgent, array $comments)
    {
        $this->userAgent = $userAgent;
        $this->comments = $comments;
    }

    /**
     * Get
     *
     * @return string[]
     */
    public function get()
    {
        return $this->userAgent === self::USER_AGENT ? [] : $this->export();
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        return $this->comments;
    }
}
