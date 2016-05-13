<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;

/**
 * Class RobotVersion
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RobotVersion implements DirectiveInterface, RobotsTxtInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_ROBOT_VERSION;

    /**
     * RobotVersion value
     * @var float|int|string
     */
    protected $value;

    /**
     * RobotVersion constructor.
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
        if (!empty($this->value)) {
            return false;
        }
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
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }
}
