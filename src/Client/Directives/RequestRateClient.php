<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class RequestRateClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/RequestRateClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class RequestRateClient extends DelayCore
{
    use DirectiveClientTrait;

    /**
     * Rates
     * @var array
     */
    private $rates = [];

    /**
     * Fallback value
     * @var float|int
     */
    private $fallbackValue;

    /**
     * RequestRateClient constructor.
     *
     * @param string $baseUri
     * @param string $product
     * @param array $rates
     * @param float|int $fallbackValue
     */
    public function __construct($baseUri, $product, array $rates, $fallbackValue = 0)
    {
        parent::__construct($baseUri, $product);
        $this->rates = $rates;
        $this->fallbackValue = $fallbackValue;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return $this->rates;
    }

    /**
     * Get rate
     *
     * @param int|null $timestamp
     * @return float|int
     */
    public function getValue($timestamp = null)
    {
        $values = $this->determine(is_int($timestamp) ? $timestamp : time());
        if (count($values) > 0 &&
            ($rate = min($values)) > 0
        ) {
            return $rate;
        }
        return $this->fallbackValue;
    }

    /**
     * Determine rates
     *
     * @param int $timestamp
     * @return float[]|int[]
     */
    private function determine($timestamp)
    {
        $values = [];
        foreach ($this->rates as $array) {
            if (!isset($array['from']) ||
                !isset($array['to'])
            ) {
                $values[] = $array['rate'];
                continue;
            }
            if ($this->isBetween($timestamp, $array['from'], $array['to'], 'Hi')) {
                $values[] = $array['rate'];
            }
        }
        return $values;
    }
}
