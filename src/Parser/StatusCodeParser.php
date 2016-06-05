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
     * Replacement coded
     * @var array
     */
    protected $unofficialCodes = [
        522 => 408, // CloudFlare could not negotiate a TCP handshake with the origin server.
        523 => 404, // CloudFlare could not reach the origin server; for example, if the DNS records for the baseUrl server are incorrect.
        524 => 408, // CloudFlare was able to complete a TCP connection to the origin server, but did not receive a timely HTTP response.
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
     * Replace unofficial code
     *
     * @param int[] $codePairs
     * @return int
     */
    public function codeOverride($codePairs = [])
    {
        $pairs = empty($codePairs) ? $this->unofficialCodes : $codePairs;
        while (in_array($this->code, array_keys($pairs))) {
            $this->code = $pairs[$this->code];
        }
        return $this->code;
    }

    /**
     * Check
     *
     * @return string|null
     */
    public function accessOverrideCheck()
    {
        if (!$this->applicable) {
            return null;
        }
        switch (floor($this->code / 100) * 100) {
            case 300:
            case 400:
                return self::DIRECTIVE_ALLOW;
            case 500:
                return self::DIRECTIVE_DISALLOW;
        }
        return null;
    }
}
