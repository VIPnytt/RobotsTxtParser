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
     * VisitTime array
     * @var array
     */
    private $visitTimes = [];

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
            $this->visitTimes[] = $array;
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
        return new VisitTimeClient($this->visitTimes);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->visitTimes as $array) {
            $result[] = self::DIRECTIVE_VISIT_TIME . ':' . $array['from'] . '-' . $array['to'];
        }
        sort($result);
        return $result;
    }
}
