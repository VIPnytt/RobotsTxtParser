<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use DateTime;
use DateTimeZone;
use vipnytt\RobotsTxtParser\Handler\ErrorHandler;

/**
 * Trait DirectiveClientTrait
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
trait DirectiveClientTrait
{
    /**
     * Is time between
     *
     * @param int $timestamp
     * @param string $fromTime
     * @param string $toTime
     * @param string $format
     * @return bool
     */
    private function isBetween($timestamp, $fromTime, $toTime, $format = 'Hi')
    {
        $dateTime = new DateTime();
        $timezone = new DateTimeZone('UTC');
        $dtRef = $dateTime->createFromFormat('U', $timestamp, $timezone);
        $dtFrom = $dateTime->createFromFormat($format, $fromTime, $timezone);
        $dtTo = $dateTime->createFromFormat($format, $toTime, $timezone);
        if ($dtFrom > $dtTo) {
            $dtTo->modify('+1 day');
        }
        return (
                $dtFrom <= $dtRef &&
                $dtRef <= $dtTo
            ) || (
                $dtFrom <= $dtRef->modify('+1 day') &&
                $dtRef <= $dtTo
            );
    }

    /**
     * Check path rule
     *
     * @param string $path
     * @param string[] $paths
     * @return int|false
     */
    private function checkPaths($path, array $paths)
    {
        $pairs = [
            '?' => '\?',
            '.' => '\.',
            '*' => '.*',
        ];
        foreach ($paths as $rule) {
            $escaped = $rule;
            foreach ($pairs as $search => $replace) {
                $escaped = str_replace($search, $replace, $escaped);
            }
            if ($this->checkPathsCallback($escaped, $path)) {
                return mb_strlen($rule);
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
         * @link https://github.com/t1gor/Robots.txt-Parser-Class/issues/59
         *
         * An robots.txt parser, where a bug-fix is/was planned
         * @link https://github.com/diggin/Diggin_RobotRules
         *
         * References:
         * @link https://github.com/diggin/Diggin_RobotRules/blob/d5fe3c7a41be28dcd20fafee3ed4297dbc9e0378/README.markdown
         * @link https://github.com/diggin/Diggin_RobotRules/commit/d5fe3c7a41be28dcd20fafee3ed4297dbc9e0378#diff-0a369498a5a8db3ac8fa606b544c9810R19
         *
         * The solution?
         * PHP PEG (parsing expression grammar)
         * @link https://github.com/hafriedlander/php-peg
         */
        $errorHandler = new ErrorHandler();
        set_error_handler([$errorHandler, 'callback'], E_NOTICE | E_WARNING);
        if (preg_match('#' . $rule . '#', $path) === false) {
            // Rule does not match
            restore_error_handler();
            return false;
        } elseif (mb_strpos($rule, '$') === false || // No end anchor, return true
            mb_substr($rule, 0, -1) === $path // End anchor detected, path exact match, return true
        ) {
            restore_error_handler();
            return true;
        } elseif (($wildcardPos = mb_strrpos($rule, '*')) !== false) {
            // Rule contains both an end anchor ($) and wildcard (*)
            $afterWildcard = mb_substr($rule, $wildcardPos + 1, mb_strlen($rule) - $wildcardPos - 2);
            if ($afterWildcard == mb_substr($path, -mb_strlen($afterWildcard))) {
                restore_error_handler();
                return true;
            }
        }
        restore_error_handler();
        return false;
    }
}
