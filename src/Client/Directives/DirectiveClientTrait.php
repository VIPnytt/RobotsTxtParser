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
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Trait DirectiveClientTrait
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
trait DirectiveClientTrait
{
    /**
     * Get path and query
     *
     * @param string $uri
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getPathFromUri($uri)
    {
        $uriParser = new UriParser($uri);
        // Prepare uri
        $uriParser->encode();
        $uri = $uriParser->stripFragment();
        if (strpos($uri, '/') === 0) {
            // URI is already an path
            return $uri;
        }
        if (!$uriParser->validate()) {
            throw new \InvalidArgumentException('Invalid URI');
        }
        $path = (($path = parse_url($uri, PHP_URL_PATH)) === null) ? '/' : $path;
        $query = (($query = parse_url($uri, PHP_URL_QUERY)) === null) ? '' : '?' . $query;
        return $path . $query;
    }

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
     * @return string|false
     */
    private function checkPaths($path, array $paths)
    {
        $reserved = [
            '?' => '\?',
            '.' => '\.',
            '*' => '.*',
            '+' => '\+',
            '(' => '\(',
            ')' => '\)',
            '[' => '\[',
            ']' => '\]',
        ];
        //$errorHandler = new ErrorHandler();
        //set_error_handler([$errorHandler, 'callback'], E_NOTICE | E_WARNING);
        foreach ($paths as $rule) {
            $escaped = str_replace(array_keys($reserved), array_values($reserved), $rule);
            if (preg_match('#^' . $escaped . '#', $path) === 1) {
                //restore_error_handler();
                return $rule;
            }
        }
        //restore_error_handler();
        return false;
    }
}
