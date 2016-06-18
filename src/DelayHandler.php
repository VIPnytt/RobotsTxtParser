<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Directives\DelayHandlerClient;
use vipnytt\RobotsTxtParser\Client\Directives\DelayInterface;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\SQL\SQLInterface;

/**
 * Class DelayHandler
 *
 * @package vipnytt\RobotsTxtParser
 */
class DelayHandler implements SQLInterface
{
    use UrlParser;

    /**
     * Database connection
     * @var PDO
     */
    private $pdo;

    /**
     * DelayHandler constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if ($this->pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_SILENT) {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $this->pdo->exec('SET NAMES ' . self::SQL_ENCODING);
    }

    /**
     * Client
     *
     * @param DelayInterface $client
     * @return DelayHandlerClient
     */
    public function client(DelayInterface $client)
    {
        return new DelayHandlerClient($this->pdo, $client->getBaseUri(), $client->getUserAgent(), $client->getValue());
    }

    /**
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 60)
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE microTime < ((UNIX_TIMESTAMP() - :delay) * 1000000);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }
}
