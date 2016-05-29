<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RobotVersionParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RobotVersionParser implements ParserInterface, RobotsTxtInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_ROBOT_VERSION;

    /**
     * RobotVersion value
     * @var float|int|string
     */
    private $value;

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
     * Export rules
     *
     * @return float[][]|int[][]|string[][]
     */
    public function export()
    {
        return empty($this->value) ? [] : [self::DIRECTIVE => $this->value];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        if (!empty($this->value)) {
            return [self::DIRECTIVE . ':' . $this->value];
        }
        return [];
    }
}
