<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class HttpRetryAfterTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class HttpRetryAfterTest extends TestCase
{
    /**
     * Random URIs that *might* return HTTP 503 AND have an Retry-after header
     * @var string[]
     */
    protected $uriPool = [
        'http://www.erasmusjournalisten.nl',
        'http://helenerask.no',
        'http://geeko.lesoir.be',
    ];

    public function testHttpRetryAfter()
    {
        $pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $count = 0;
        foreach ($this->uriPool as $uri) {
            $cache = new RobotsTxtParser\Cache($pdo);
            $this->assertInstanceOf('vipnytt\RobotsTxtParser\Cache', $cache);
            $cache->invalidate($uri);
            $halfTime = time() + 43200; // 12 hours
            $cache->client($uri);
            $debug = $cache->debug($uri);
            if ($debug['statusCode'] == 503 &&
                $halfTime > $debug['nextUpdate']
            ) {
                $count++;
            }
        }
        if ($count === 0) {
            $this->markTestSkipped("No URLs returned both `HTTP 503` and the `Retry-after` header. NB! Such circumstances are uncommon, and if the criteria is met, it's only a temporary state.");
        }
    }
}
