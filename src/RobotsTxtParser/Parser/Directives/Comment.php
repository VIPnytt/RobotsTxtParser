<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;

/**
 * Class Comment
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class Comment implements DirectiveInterface, RobotsTxtInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_COMMENT;

    /**
     * Comment array
     * @var string
     */
    protected $value;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add
     *
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        $this->value = $line;
        return true;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return empty($this->value) ? [] : [self::DIRECTIVE => $this->value];
    }
}
