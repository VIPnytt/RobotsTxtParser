<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class MysqlCacheDisplaceTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class MysqlCacheDisplaceTest extends TestCase
{
    /**
     * Test that the robots.txt content is cached for longer than 24h in case of failures when updating the cache
     *
     * This test searches for hosts with an http status code of 500 when fetching the robots.txt.
     * If none is found, then the test fails.
     *
     * @throws RobotsTxtParser\Exceptions\DatabaseException
     */
    public function testCacheDisplace()
    {
        // This URL list is based on real data. Update when needed!
        // It loops thou the list, until an http 500 match is found. Therefore not all URL are fetched...
        $bases = [
            'http://acheterdesvues.fr:80',
            'http://basbaassauce.com:80',
            'http://businesswireindia.com:80',
            'http://chimamanda.com:80',
            'http://feed.web20share.com:80',
            'http://gdn-got.appspot.com:80',
            'http://gorgeousfestival.com.au:80',
            'http://ieghee1ee.tk:80',
            'http://neumanmethod.com:80',
            'http://parks.nv.gov:80',
            'http://pflagdetroit.org:80',
            'http://primary.quickstartcomputing.org:80',
            'http://rss.vanitatis.elconfidencial.com:80',
            'http://thecoraltriangle.com:80',
            'http://tietotrenditblogi.stat.fi:80',
            'http://triviamonster.io:80',
            'http://www.allanclayton.com:80',
            'http://www.android-software.fr:80',
            'http://www.bakersmall.co.uk:80',
            'http://www.blogsmithmedia.com:80',
            'http://www.flicharge.com:80',
            'http://www.immerex.com:80',
            'http://www.interior.go.ke:80',
            'http://www.lamaisondete.com:80',
            'http://www.miljodirektoratet.no:80',
            'http://www.quilliamfoundation.org:80',
            'http://www.theaustralian.news.com.au:80',
            'http://www.theskimm.com:80',
            'http://www.tinhouse.com:80',
            'http://www.ukslhp.org:80',
            'https://gearsofwar.com:443',
            'https://hungryhouse.co.uk:443',
            'https://s.blogsmithmedia.com:443',
            'https://www.aei.org:443',
            'https://www.edoramedia.com:443',
        ];
        $result = false;
        foreach ($bases as $base) {
            if ($result) {
                continue;
            }
            $result = $this->check($base);
        }
        $this->assertTrue($result);
    }

    /**
     * @param string $baseUri
     * @return bool
     * @throws RobotsTxtParser\Exceptions\DatabaseException
     */
    private function check($baseUri)
    {
        $pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $cache = (new RobotsTxtParser\Database($pdo))->cache();
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Cache\ManageInterface', $cache);

        $base = $cache->base($baseUri);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Cache\BaseInterface', $base);

        // Insert fake data
        $base->invalidate();
        $query = $pdo->prepare(<<<SQL
INSERT INTO robotstxt__cache1 (base, content, statusCode, validUntil, nextUpdate)
VALUES (:base, '', NULL, UNIX_TIMESTAMP() + 86400, UNIX_TIMESTAMP() - 3600);
SQL
        );
        $query->bindParam('base', $baseUri, \PDO::PARAM_STR);
        $query->execute();

        $cache->cron();

        // Check if update has been displaced
        $query = $pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__cache1
WHERE base = :base AND validUntil > UNIX_TIMESTAMP() AND nextUpdate > UNIX_TIMESTAMP() AND statusCode IS NULL;
SQL
        );
        $query->bindParam('base', $baseUri, \PDO::PARAM_STR);
        $query->execute();
        // Delete fake data
        $base->invalidate();
        if ($query->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
