<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;

/**
 * Class RequestRateClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/RequestRateClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class RequestRateClient extends DelayCore implements ClientInterface, DelayInterface
{
    use DirectiveClientCommons;

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
     * @param string $userAgent
     * @param array $rates
     * @param float|int $fallbackValue
     */
    public function __construct($baseUri, $userAgent, array $rates, $fallbackValue = 0)
    {
        $this->rates = $rates;
        $this->fallbackValue = $fallbackValue;
        parent::__construct($baseUri, $userAgent);
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
     * Handle delay
     *
     * @param PDO $pdo
     * @return DelayHandlerClient
     */
    public function handle(PDO $pdo)
    {
        return new DelayHandlerClient($pdo, $this->base, $this->userAgent, $this->getValue());
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
        if (
            count($values) > 0 &&
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
            if (
                !isset($array['from']) ||
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
