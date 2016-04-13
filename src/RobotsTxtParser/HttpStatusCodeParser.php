<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions;

class HttpStatusCodeParser
{
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
     * @return string
     */
    public function isAllowed()
    {
        switch (floor($this->code / 100) * 100) {
            case 500:
                return false;
        }
        return true;
    }
}
