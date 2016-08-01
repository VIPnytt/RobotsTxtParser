<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class VisitTimeClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/VisitTimeClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class VisitTimeClient implements ClientInterface
{
    use DirectiveClientTrait;

    /**
     * Times
     * @var array
     */
    private $times = [];

    /**
     * RequestRateClient constructor.
     *
     * @param array $times
     */
    public function __construct(array $times)
    {
        $this->times = $times;
    }

    /**
     * Is visit-time
     *
     * @param int|null $timestamp
     * @return bool
     */
    public function isVisitTime($timestamp = null)
    {
        $timestamp = is_int($timestamp) ? $timestamp : time();
        foreach ($this->times as $time) {
            if ($this->isBetween($timestamp, $time['from'], $time['to'], 'Hi')) {
                return true;
            }
        }
        return empty($this->times);
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return $this->times;
    }
}
