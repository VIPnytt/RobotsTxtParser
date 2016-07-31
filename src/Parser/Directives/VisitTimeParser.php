<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\VisitTimeClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class VisitTimeParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class VisitTimeParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserTrait;

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
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        $this->sort();
        foreach ($this->visitTimes as $array) {
            $handler->add(self::DIRECTIVE_VISIT_TIME, $array['from'] . '-' . $array['to']);
        }
        return true;
    }

    /**
     * Sort
     *
     * @return void
     */
    private function sort()
    {
        usort($this->visitTimes, function ($a, $b) {
            // PHP 7: Switch to the <=> "Spaceship" operator
            return $a['from'] > $b['from'];
        });
    }
}
