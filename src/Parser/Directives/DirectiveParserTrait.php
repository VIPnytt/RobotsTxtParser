<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use DateTimeZone;

/**
 * Class DirectiveParserTrait
 *
 * @package vipnytt\RobotsTxtParser\Directive
 */
trait DirectiveParserTrait
{
    /**
     * Generate directive/rule pair
     *
     * @param string $line
     * @param string[] $whiteList
     * @return string[]|false
     */
    private function generateRulePair($line, array $whiteList)
    {
        // Split by directive and rule
        $pair = array_map('trim', explode(':', $line, 2));
        // Check if the line contains a rule
        if (empty($pair[1]) ||
            empty($pair[0]) ||
            !in_array(($pair[0] = str_ireplace(array_keys(self::ALIAS_DIRECTIVES), array_values(self::ALIAS_DIRECTIVES), strtolower($pair[0]))), $whiteList)
        ) {
            // Line does not contain any supported directive
            return false;
        }
        return $pair;
    }

    /**
     * Client timestamp range as specified in the `Robot exclusion standard` version 2.0 draft
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.visit-time
     *
     * @param $string
     * @return string[]|false
     */
    private function draftParseTime($string)
    {
        $array = explode('-', str_replace('+', '', filter_var($string, FILTER_SANITIZE_NUMBER_INT)), 3);
        if (count($array) !== 2 ||
            ($fromTime = date_create_from_format('Hi', $array[0], $dtz = new DateTimeZone('UTC'))) === false ||
            ($toTime = date_create_from_format('Hi', $array[1], $dtz)) === false
        ) {
            return false;
        }
        return [
            'from' => date_format($fromTime, 'Hi'),
            'to' => date_format($toTime, 'Hi'),
        ];
    }
}
