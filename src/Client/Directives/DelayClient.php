<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\DelayHandlerClient;

/**
 * Class DelayClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class DelayClient extends DelayCore implements ClientInterface
{
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
        $this->exportValue = $value;
        $this->value = $value > 0 ? $value : $fallbackValue;
        parent::__construct($baseUri, $userAgent);
    }

    /**
     * Get value
     *
     * @return float|int
     */
    public function getValue()
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
     * Handle delay
     *
     * @param PDO $pdo
     * @return DelayHandlerClient
     */
    public function handle(PDO $pdo)
    {
        return new DelayHandlerClient($pdo, $this->base, $this->userAgent, $this->value);
    }
}
