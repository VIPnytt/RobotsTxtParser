<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CommentClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CommentClient implements ClientInterface, RobotsTxtInterface
{
    /**
     * Base uri
     * @var string
     */
    private $base;

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
     * Fetched status
     * @var bool
     */
    private $fetched = false;

    /**
     * CommentClient constructor.
     *
     * @param string $base
     * @param string $userAgent
     * @param array $comments
     */
    public function __construct($base, $userAgent, array $comments)
    {
        $this->base = $base;
        $this->userAgent = $userAgent;
        $this->comments = $comments;
    }

    /**
     * CommentClient destructor.
     */
    public function __destruct()
    {
        if ($this->fetched !== true) {
            // Comment exists, but has not been fetched.
            foreach ($this->get() as $message) {
                trigger_error('`' . $this->userAgent . '` at `' . $this->base . self::PATH . '` -> ' . $message, E_USER_NOTICE);
            }
        }
    }

    /**
     * Get
     *
     * @return string[]
     */
    public function get()
    {
        $this->fetched = true;
        return $this->userAgent == self::USER_AGENT ? [] : $this->export();
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        $this->fetched = true;
        return $this->comments;
    }
}
