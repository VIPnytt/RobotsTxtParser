<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use DateTime;
use DateTimeZone;

/**
 * Class DirectiveClientCommons
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
trait DirectiveClientCommons
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
}
