<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\RequestRateClient;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RequestRateParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RequestRateParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

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
        ];
        if ($result['rate'] === false) {
            return false;
        } elseif (
            !empty($array[1]) &&
            ($times = $this->draftParseTime($array[1])) !== false
        ) {
            $result = array_merge($result, $times);
        }
        $this->requestRates[] = $result;
        return true;
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
        return new RequestRateClient($this->base, $userAgent, $this->requestRates, $fallbackValue);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->requestRates as $array) {
            $suffix = 's';
            if (
                isset($array['from']) &&
                isset($array['to'])
            ) {
                $suffix .= ' ' . $array['from'] . '-' . $array['to'];
            }
            $result[] = self::DIRECTIVE_REQUEST_RATE . ':1/' . $array['rate'] . $suffix;
        }
        sort($result);
        return $result;
    }
}
