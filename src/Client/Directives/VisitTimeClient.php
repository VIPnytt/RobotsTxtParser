<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class VisitTimeClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class VisitTimeClient
{
    use DirectiveClientCommons;

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
        if (empty($this->times)) {
            return true;
        }
        return false;
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
