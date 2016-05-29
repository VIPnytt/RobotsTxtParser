<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class CommentClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CommentClient
{
    /**
     * Comments
     * @var string[]
     */
    private $comments = [];

    /**
     * CommentClient constructor.
     *
     * @param array $comments
     */
    public function __construct(array $comments)
    {
        $this->comments = $comments;
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
