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
     * @param int $no
     * @param string $str
     * @param string $file
     * @param int $line
     * @param array $context
     * @return bool
     */
    public function callback($no, $str, $file = '', $line = 0, $context = [])
    {
        $this->log[(string)microtime(true)] = [
            'no' => $no,
            'str' => $str,
            'file' => $file,
            'line' => $line,
            'context' => $context,
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
