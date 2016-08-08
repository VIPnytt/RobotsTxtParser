<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class StatusCodeParser
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
class StatusCodeParser implements RobotsTxtInterface
{
    /**
     * Status code
     * @var int
     */
    private $code;

    /**
     * Scheme
     * @var string|false
     */
    private $scheme;

    /**
     * Constructor
     *
     * @param int|null $code - HTTP status code
     * @param string|false $scheme
     */
    public function __construct($code, $scheme)
    {
        $this->code = $code === null ? 200 : $code;
        $this->scheme = $scheme;
    }

    /**
     * Validate
     *
     * @return bool
     */
    public function isValid()
    {
        return (
            $this->code >= 100 &&
            $this->code <= 599
        );
    }

    /**
     * Check if the code overrides the robots.txt file
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#handling-http-result-codes
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#additional-info
     *
     * @return string|false
     */
    public function accessOverride()
    {
        if (strpos($this->scheme, 'http') === 0) {
            switch (floor($this->code / 100) * 100) {
                case 300:
                case 400:
                    return self::DIRECTIVE_ALLOW;
                case 500:
                    return self::DIRECTIVE_DISALLOW;
            }
        }
        return false;
    }
}
