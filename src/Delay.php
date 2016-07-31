<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\ClientInterface;
use vipnytt\RobotsTxtParser\Client\Delay\ManagerInterface;
use vipnytt\RobotsTxtParser\Client\Directives\DelayInterface;
use vipnytt\RobotsTxtParser\Handler\DatabaseHandler;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class Delay
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/Delay.md for documentation
 * @package vipnytt\RobotsTxtParser
 */
final class Delay implements ManagerInterface
{

    /**
     * SQL Driver switch
     * @var DatabaseHandler
     */
    private $switch;

    /**
     * Delay constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->switch = new DatabaseHandler($pdo);
    }

    /**
     * Client
     *
     * @param DelayInterface $client
     * @return ClientInterface
     */
    public function client(DelayInterface $client)
    {
        return $this->switch->delayClient($client->getBaseUri(), $client->getUserAgent(), $client->getValue());
    }

    /**
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 60)
    {
        return $this->switch->delayManager()->clean($delay);
    }

    /**
     * Get the RAW data
     *
     * @param string $baseUri
     * @return array
     */
    public function debug($baseUri)
    {
        $parser = new UriParser($baseUri);
        return $this->switch->delayManager()->debug($parser->base());
    }

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopWaitTimes($limit = 100, $min = 0)
    {
        return $this->switch->delayManager()->getTopWaitTimes($limit, $min);
    }
}
