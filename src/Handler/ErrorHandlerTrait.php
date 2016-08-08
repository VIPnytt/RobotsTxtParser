<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

/**
 * Class ErrorHandlerTrait
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
trait ErrorHandlerTrait
{
    /**
     * Error log
     * @var string[]
     */
    protected $errorLog = [];

    /**
     * Custom error handler
     *
     * @param int $errNo
     * @param string $errStr
     * @param string $errFile
     * @param string $errLine
     * @return bool
     */
    protected function errorHandlerCallback($errNo, $errStr, $errFile, $errLine)
    {
        $this->errorLog[(string)microtime(true)] = "lvl: " . $errNo . " | msg:" . $errStr . " | file:" . $errFile . " | ln:" . $errLine;
        return true;
    }
}
