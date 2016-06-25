<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Exceptions\StatusCodeException;
use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class StatusCodeParser
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
class StatusCodeParser implements RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Valid schemes
     */
    const VALID_SCHEME = [
        'http',
        'https',
    ];

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
     * Applicable
     * @var bool
     */
    private $applicable;

    /**
     * Constructor
     *
     * @param int|null $code - HTTP status code
     * @param string|false $scheme
     * @throws StatusCodeException
     */
    public function __construct($code, $scheme)
    {
        $this->code = $code;
        $this->scheme = $scheme;
        $this->applicable = $this->isApplicable();
    }

    /**
     * Check if URL is Applicable for Status code parsing
     *
     * @return bool
     * @throws StatusCodeException
     */
    private function isApplicable()
    {
        if (
            !in_array($this->scheme, self::VALID_SCHEME) ||
            $this->code === null
        ) {
            return false;
        } elseif (
            $this->code < 100 ||
            $this->code > 599
        ) {
            throw new StatusCodeException('Invalid HTTP status code');
        }
        return true;
    }

    /**
     * Check
     *
     * @return string|false
     */
    public function accessOverride()
    {
        if (!$this->applicable) {
            return false;
        }
        switch (floor($this->code / 100) * 100) {
            case 300:
            case 400:
                return self::DIRECTIVE_ALLOW;
            case 500:
                return self::DIRECTIVE_DISALLOW;
        }
        return false;
    }
}
