<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

/**
 * Class ErrorHandler
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
class ErrorHandler
{
    /**
     * Error log
     * @var string[]
     */
    protected $log = [];

    /**
     * ErrorHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * Callback
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return bool
     */
    public function callback($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        $this->log[(string)microtime(true)] = [
            'no' => $errno,
            'str' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'context' => $errcontext,
        ];
        return $this->handle();
    }

    /**
     * Handle
     *
     * @return bool
     */
    private function handle()
    {
        return !in_array(end($this->log)['no'], [
            E_ERROR,
            E_USER_ERROR,
        ]);
    }

    /**
     * Last error as string
     *
     * @return array|false
     */
    public function getLast()
    {
        return end($this->log);
    }
}
