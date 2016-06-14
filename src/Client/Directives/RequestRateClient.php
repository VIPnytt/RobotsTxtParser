<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\DelayHandlerClient;

/**
 * Class RequestRateClient
 *
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
     * RequestRateClient constructor.
     *
     * @param string $baseUri
     * @param string $userAgent
     * @param array $rates
     */
    public function __construct($baseUri, $userAgent, array $rates)
    {
        $this->rates = $rates;
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
     * Client
     *
     * @param PDO $pdo
     * @return DelayHandlerClient
     */
    public function client(PDO $pdo)
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
        return 0;
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
