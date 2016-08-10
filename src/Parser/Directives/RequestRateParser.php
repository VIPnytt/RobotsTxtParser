<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\RequestRateClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RequestRateParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RequestRateParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserTrait;

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * RequestRate array
     * @var array
     */
    private $requestRates = [];

    /**
     * Client cache
     * @var RequestRateClient
     */
    private $client;

    /**
     * RequestRate constructor.
     *
     * @param string $base
     */
    public function __construct($base)
    {
        $this->base = $base;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $array = preg_split('/\s+/', $line, 2);
        $result = [
            'rate' => $this->draftParseRate($array[0]),
            'from' => null,
            'to' => null,
        ];
        if ($result['rate'] === false) {
            return false;
        } elseif (!empty($array[1]) &&
            ($times = $this->draftParseTime($array[1])) !== false
        ) {
            $result = array_merge($result, $times);
        }
        $this->requestRates[] = $result;
        return true;
    }

    /**
     * Client rate as specified in the `Robot exclusion standard` version 2.0 draft
     * rate = numDocuments / timeUnit
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.request-rate
     *
     * @param string $string
     * @return float|int|false
     */
    private function draftParseRate($string)
    {
        $parts = array_map('trim', explode('/', $string));
        if (count($parts) != 2) {
            return false;
        }
        $multiplier = 1;
        switch (strtolower(substr(preg_replace('/[^A-Za-z]/', '', filter_var($parts[1], FILTER_SANITIZE_STRING)), 0, 1))) {
            case 'm':
                $multiplier = 60;
                break;
            case 'h':
                $multiplier = 3600;
                break;
            case 'd':
                $multiplier = 86400;
                break;
        }
        $rate = abs(filter_var($parts[1], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) * $multiplier / abs(filter_var($parts[0], FILTER_SANITIZE_NUMBER_INT));
        return $rate > 0 ? $rate : false;
    }

    /**
     * Client
     *
     * @param string $userAgent
     * @param float|int $fallbackValue
     * @return RequestRateClient
     */
    public function client($userAgent = self::USER_AGENT, $fallbackValue = 0)
    {
        if (isset($this->client)) {
            return $this->client;
        }
        $this->sort();
        return $this->client = new RequestRateClient($this->base, $userAgent, $this->requestRates, $fallbackValue);
    }

    /**
     * Sort
     *
     * @return void
     */
    private function sort()
    {
        usort($this->requestRates, function (array $requestRateA, array $requestRateB) {
            // PHP 7: Switch to the <=> "Spaceship" operator
            return $requestRateB['rate'] > $requestRateA['rate'];
        });
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        $this->sort();
        foreach ($this->requestRates as $array) {
            $suffix = 's';
            if (isset($array['from']) &&
                isset($array['to'])
            ) {
                $suffix .= ' ' . $array['from'] . '-' . $array['to'];
            }
            $handler->add(self::DIRECTIVE_REQUEST_RATE, '1/' . $array['rate'] . $suffix);
        }
        return true;
    }
}
