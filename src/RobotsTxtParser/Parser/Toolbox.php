<?php
namespace vipnytt\RobotsTxtParser\Parser;

use DateTimeZone;
use vipnytt\RobotsTxtParser\Exceptions\ParserException;

/**
 * Trait Toolbox
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
trait Toolbox
{
    /**
     * Check basic rule
     *
     * @param string $path
     * @param array $paths
     * @return bool
     */
    protected function checkPath($path, array $paths)
    {
        foreach ($paths as $rule) {
            $escape = ['?' => '\?', '.' => '\.', '*' => '.*'];
            foreach ($escape as $search => $replace) {
                $rule = str_replace($search, $replace, $rule);
            }
            /**
             * Warning: preg_match need to be replaced
             *
             * Bug report
             * @link https://github.com/t1gor/Robots.txt-Parser-Class/issues/62
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
                    continue;
                } else if (mb_stripos($rule, '$') === false) {
                    // No special parsing required
                    return true;
                } else if (($wildcardPos = mb_strrpos($rule, '*')) !== false) {
                    // Rule contains both an end anchor ($) and wildcard (*)
                    $afterWildcard = mb_substr($rule, $wildcardPos + 1, mb_strlen($rule) - $wildcardPos - 2);
                    if ($afterWildcard == mb_substr($path, -mb_strlen($afterWildcard))) {
                        return true;
                    }
                } else if (mb_substr($rule, 0, -1) == $path) {
                    // Rule does contains an end anchor
                    return true;
                }
            } catch (\Exception $e) {
                // An preg_match bug has occurred
            }
        }
        return false;
    }

    /**
     * Generate directive/rule pair
     *
     * @param string $line
     * @param array $whiteList
     * @return array|false
     */
    protected function generateRulePair($line, array $whiteList)
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
     * Validate directive
     *
     * @param string $directive
     * @param array $directives
     * @return string
     * @throws ParserException
     */
    protected function validateDirective($directive, array $directives)
    {
        if (!in_array($directive, $directives, true)) {
            throw new ParserException('Directive is not allowed here');
        }
        return mb_strtolower($directive);
    }

    /**
     * Parse rate as specified in the `Robot exclusion standard` version 2.0 draft
     * rate = numDocuments / timeUnit
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.request-rate
     *
     * @param $string
     * @return int|float|false
     */
    protected function draftParseRate($string)
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
        $num = intval(preg_replace('/[^0-9]/', '', $parts[0]));
        $sec = intval(preg_replace('/[^0-9]/', '', $parts[1])) * $multiplier;
        $rate = $num / $sec;
        return $rate > 0 ? $rate : false;
    }

    /**
     * Parse timestamp range as specified in the `Robot exclusion standard` version 2.0 draft
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.visit-time
     *
     * @param $string
     * @return array|bool
     */
    protected function draftParseTime($string)
    {
        $array = preg_replace('/[^0-9]/', '', mb_split('-', $string));
        if (
            count($array) != 2 ||
            ($from = date_create_from_format('Hi', $array[0], new DateTimeZone('UTC'))) === false ||
            ($to = date_create_from_format('Hi', $array[1], new DateTimeZone('UTC'))) === false
        ) {
            return false;
        }
        return [
            'from' => date_format($from, 'Hi'),
            'to' => date_format($to, 'Hi'),
        ];
    }
}
