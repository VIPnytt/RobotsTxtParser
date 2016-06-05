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
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * RequestRate array
     * @var array
     */
    private $requestRates = [];

    /**
     * RequestRate constructor.
     *
     * @param string $base
     * @param string $userAgent
     */
    public function __construct($base, $userAgent)
    {
        $this->base = $base;
        $this->userAgent = $userAgent;
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
     * @return RequestRateClient
     */
    public function client()
    {
        return new RequestRateClient($this->base, $this->userAgent, $this->requestRates);
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
