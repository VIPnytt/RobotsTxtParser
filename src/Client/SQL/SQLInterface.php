<?php
namespace vipnytt\RobotsTxtParser\Client\SQL;

/**
 * Interface SQLInterface
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL
 */
interface SQLInterface
{
    /**
     * Cache table
     */
    const TABLE_CACHE = 'robotstxt__cache0';

    /**
     * Delay table
     */
    const TABLE_DELAY = 'robotstxt__delay0';

    /**
     * SQL Readme
     */
    const README_SQL = 'https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/README.md';

    /**
     * Cache readme
     */
    const README_SQL_CACHE = 'https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/cache.md';

    /**
     * Delay readme
     */
    const README_SQL_DELAY = 'https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/delay.md';
}
