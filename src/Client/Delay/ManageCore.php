<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay;

/**
 * Class ManageCore
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayInterface.md for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Delay
 */
abstract class ManageCore implements ManageInterface
{
    /**
     * Database handler
     * @var \PDO
     */
    protected $pdo;

    /**
     * ManageCore constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
