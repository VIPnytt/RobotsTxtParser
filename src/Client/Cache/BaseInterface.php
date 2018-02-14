<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache;

use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;
use vipnytt\RobotsTxtParser\Exceptions\OutOfSyncException;
use vipnytt\RobotsTxtParser\TxtClient;

/**
 * Interface BaseInterface
 *
 * @package vipnytt\RobotsTxtParser\Client\Cache
 */
interface BaseInterface extends CacheInterface
{
    /**
     * Line separator
     */
    const RENDER_LINE_SEPARATOR = "\n";

    /**
     * Debug - get raw data
     *
     * @return array
     */
    public function debug();

    /**
     * Invalidate cache
     *
     * @return bool
     */
    public function invalidate();

    /**
     * Parser client
     *
     * @return TxtClient
     * @throws OutOfSyncException
     * @throws DatabaseException
     */
    public function client();
}
