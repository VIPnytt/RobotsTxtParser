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
}
