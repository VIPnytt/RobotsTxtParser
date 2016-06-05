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
     * Check path rule
     *
     * @param string $path
     * @param string[] $paths
     * @return bool
     */
    private function checkPaths($path, array $paths)
    {
        foreach ($paths as $rule) {
            $escape = [
                '?' => '\?',
                '.' => '\.',
                '*' => '.*',
            ];
            foreach ($escape as $search => $replace) {
                $rule = str_replace($search, $replace, $rule);
            }
            if ($this->checkPathsCallback($rule, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Callback for CheckPath
     *
     * @param string $rule
     * @param string $path
     * @return bool
     */
    private function checkPathsCallback($rule, $path)
    {
        /**
         * Warning: preg_match need to be replaced
         *
         * Bug report
         * @link https://github.com/t1gor/Robots.txt-Core-Class/issues/62
         *
         * An robots.txt parser, where a bug-fix is planned
         * @link https://github.com/diggin/Diggin_RobotRules
         *
         * The solution?
         * PHP PEG (parsing expression grammar)
         * @link https://github.com/hafriedlander/php-peg
         */
        try {
            if (!preg_match('#' . $rule . '#', $path)) {
                // Rule does not match
                return false;
            } elseif (
                mb_stripos($rule, '$') === false || // No special parsing required
                mb_substr($rule, 0, -1) == $path // Rule does contain an end anchor, and matches
            ) {
                return true;
            } elseif (($wildcardPos = mb_strrpos($rule, '*')) !== false) {
                // Rule contains both an end anchor ($) and wildcard (*)
                $afterWildcard = mb_substr($rule, $wildcardPos + 1, mb_strlen($rule) - $wildcardPos - 2);
                if ($afterWildcard == mb_substr($path, -mb_strlen($afterWildcard))) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // An preg_match bug has occurred
        }
        return false;
    }

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
            !in_array(($pair[0] = mb_strtolower($pair[0])), $whiteList)
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
