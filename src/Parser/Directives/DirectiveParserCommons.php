<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use DateTimeZone;
use vipnytt\RobotsTxtParser\Exceptions\ParserException;

/**
 * Class DirectiveParserCommons
 *
 * @package vipnytt\RobotsTxtParser\Directive
 */
trait DirectiveParserCommons
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
        $whiteList = array_map('mb_strtolower', $whiteList);
        // Split by directive and rule
        $pair = array_map('trim', mb_split(':', $line, 2));
        // Check if the line contains a rule
        if (
            empty($pair[1]) ||
            empty($pair[0]) ||
            !in_array(($pair[0] = str_ireplace(array_keys(self::ALIAS_DIRECTIVES), array_values(self::ALIAS_DIRECTIVES), mb_strtolower($pair[0]))), $whiteList)
        ) {
            // Line does not contain any supported directive
            return false;
        }
        return [
            'directive' => $pair[0],
            'value' => $pair[1],
        ];
    }

    /**
     * Client rate as specified in the `Robot exclusion standard` version 2.0 draft
     * rate = numDocuments / timeUnit
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.request-rate
     *
     * @param string $string
     * @return float|int|false
     */
    private function draftParseRate($string)
    {
        $parts = array_map('trim', mb_split('/', $string));
        if (count($parts) != 2) {
            return false;
        }
        $multiplier = 1;
        switch (mb_substr(mb_strtolower(preg_replace('/[^A-Za-z]/', '', $parts[1])), 0, 1)) {
            case 'm':
                $multiplier = 60;
                break;
            case 'h':
                $multiplier = 3600;
                break;
            case 'd':
                $multiplier = 86400;
                break;
        }
        $num = floatval(preg_replace('/[^0-9]/', '', $parts[0]));
        $sec = floatval(preg_replace('/[^0-9.]/', '', $parts[1])) * $multiplier;
        $rate = $sec / $num;
        return $rate > 0 ? $rate : false;
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
        $array = preg_replace('/[^0-9]/', '', mb_split('-', $string));
        if (
            count($array) != 2 ||
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

    /**
     * Validate directive
     *
     * @param string $directive
     * @param string[] $directives
     * @return string
     * @throws ParserException
     */
    private function validateDirective($directive, array $directives)
    {
        if (!in_array($directive, $directives, true)) {
            throw new ParserException('Directive not supported by this class');
        }
        return mb_strtolower($directive);
    }
}
