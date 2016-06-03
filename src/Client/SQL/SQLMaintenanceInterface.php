<?php
namespace vipnytt\RobotsTxtParser\Client\SQL;

/**
 * Interface SQLMaintenanceInterface
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL
 */
interface SQLMaintenanceInterface
{
    /**
     * Cleaning
     *
     * @return bool
     */
    public function clean();

    /**
     * Table setup
     *
     * @return bool
     */
    public function setup();
}
