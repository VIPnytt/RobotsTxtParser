<?php
namespace vipnytt\RobotsTxtParser\Core\Directives;

use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Core\Toolbox;

/**
 * Class VisitTime
 *
 * @package vipnytt\RobotsTxtParser\Core\Directives
 */
class VisitTime implements DirectiveInterface, RobotsTxtInterface
{
    use Toolbox;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_VISIT_TIME;

    /**
     * VisitTime array
     * @var array
     */
    protected $array = [];

    /**
     * VisitTime constructor.
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
        $array = $this->draftParseTime($line);
        if ($array !== false) {
            $this->array[] = $array;
            return true;
        }
        return false;
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
        foreach ($this->array as $array) {
            $result[] = self::DIRECTIVE . ':' . $array['from'] . '-' . $array['to'];
        }
        return $result;
    }
}
