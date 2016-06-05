<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\Delay\DelayHandlerSQL;

/**
 * Interface DelayInterface
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
interface DelayInterface
{
    /**
     * Get
     *
     * @return float|int
     */
    public function get();

    /**
     * SQL back-end
     *
     * @param PDO $pdo
     * @return DelayHandlerSQL
     */
    public function sql(PDO $pdo);
}
