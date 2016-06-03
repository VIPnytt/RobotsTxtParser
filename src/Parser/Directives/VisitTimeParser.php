<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\VisitTimeClient;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class VisitTimeParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class VisitTimeParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_VISIT_TIME;

    /**
     * VisitTime array
     * @var array
     */
    private $array = [];

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
     * Client
     *
     * @return VisitTimeClient
     */
    public function client()
    {
        return new VisitTimeClient($this->array);
    }

    /**
     * Rule array
     *
     * @return string[][]
     */
    public function getRules()
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
