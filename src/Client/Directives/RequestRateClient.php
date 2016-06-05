<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\Delay\DelayHandlerSQL;

/**
 * Class RequestRateClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class RequestRateClient implements DelayInterface, ClientInterface
{
    use DirectiveClientCommons;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * User-agent
     * @var string
     */
    private $userAgent;

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
        $this->base = $baseUri;
        $this->userAgent = $userAgent;
        $this->rates = $rates;
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
     * SQL back-end
     *
     * @param PDO $pdo
     * @return DelayHandlerSQL
     */
    public function sql(PDO $pdo)
    {
        return new DelayHandlerSQL($pdo, $this->base, $this->userAgent, $this->get());
    }

    /**
     * Get rate for current timestamp
     *
     * @param int|null $timestamp
     * @return float|int
     */
    public function get($timestamp = null)
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
