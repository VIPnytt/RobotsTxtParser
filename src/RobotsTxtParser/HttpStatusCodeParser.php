<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions;

class HttpStatusCodeParser implements RobotsTxtInterface
{
    /**
     * Directive alternatives
     */
    const DIRECTIVES = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_DISALLOW,
    ];

    /**
     * Status code
     * @var int
     */
    protected $code = 200;

    /**
     * Replacement coded
     * @var array
     */
    protected $unofficialCodes = [
        522 => 408, // CloudFlare could not negotiate a TCP handshake with the origin server.
        523 => 404, // CloudFlare could not reach the origin server; for example, if the DNS records for the origin server are incorrect.
        524 => 408, // CloudFlare was able to complete a TCP connection to the origin server, but did not receive a timely HTTP response.
    ];

    /**
     * Constructor
     *
     * @param integer $code - HTTP status code
     * @throws Exceptions\HttpStatusCodeException
     */
    public function __construct($code)
    {
        if (!is_int($code) ||
            $code < 100 ||
            $code > 599
        ) {
            throw new Exceptions\HttpStatusCodeException('Invalid HTTP status code');
        }
        $this->code = $code;
    }

    /**
     * Replace an unofficial code
     *
     * @return int|false
     */
    public function replaceUnofficial()
    {
        if (in_array($this->code, array_keys($this->unofficialCodes))) {
            $this->code = $this->unofficialCodes[$this->code];
            return $this->code;
        }
        return false;
    }

    /**
     * Determine the correct group
     *
     * @param string $directive
     * @return bool|null
     * @throws Exceptions\ClientException
     */
    public function isAllowed($directive = self::DIRECTIVE_ALLOW)
    {
        if (!in_array($directive, self::DIRECTIVES, true)) {
            throw new Exceptions\ClientException('Directive not allowed here, has to be `' . self::DIRECTIVE_ALLOW . '` or `' . self::DIRECTIVE_DISALLOW . '`');
        }
        switch (floor($this->code / 100) * 100) {
            case 400:
                return $directive === self::DIRECTIVE_ALLOW;
            case 500:
                return $directive === self::DIRECTIVE_DISALLOW;
        }
        return null;
    }
}
