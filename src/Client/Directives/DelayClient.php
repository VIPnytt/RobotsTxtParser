<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\Delay\DelayHandlerSQL;

/**
 * Class DelayClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class DelayClient
{
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
     * Value
     * @var float|int
     */
    private $value;

    /**
     * Export value
     * @var float|int
     */
    private $exportValue;

    /**
     * DelayClient constructor.
     *
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $value
     * @param float|int $fallbackValue
     */
    public function __construct($baseUri, $userAgent, $value, $fallbackValue = 0)
    {
        $this->base = $baseUri;
        $this->userAgent = $userAgent;
        $this->exportValue = $value;
        $this->value = $value > 0 ? $value : $fallbackValue;
    }

    /**
     * Get
     *
     * @return float|int
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Export
     *
     * @return float|int
     */
    public function export()
    {
        return $this->exportValue;
    }

    /**
     * SQL back-end
     *
     * @param PDO $pdo
     * @return DelayHandlerSQL
     */
    public function sql(PDO $pdo)
    {
        return new DelayHandlerSQL($pdo, $this->base, $this->userAgent, $this->value);
    }
}
