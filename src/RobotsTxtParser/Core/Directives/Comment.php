<?php
namespace vipnytt\RobotsTxtParser\Core\Directives;

use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;

/**
 * Class Comment
 *
 * @package vipnytt\RobotsTxtParser\Core\Directives
 */
class Comment implements DirectiveInterface, RobotsTxtInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_COMMENT;

    /**
     * Comment array
     * @var string[]
     */
    protected $array = [];

    /**
     * Comment constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $this->array[] = $line;
        return true;
    }

    /**
     * Export rules
     *
     * @return string[][]
     */
    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->array as $value) {
            $result[] = self::DIRECTIVE . ':' . $value;
        }
        return $result;
    }
}
