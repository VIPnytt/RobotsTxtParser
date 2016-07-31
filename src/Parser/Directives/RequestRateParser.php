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
     * Client
     *
     * @param string $userAgent
     * @param float|int $fallbackValue
     * @return RequestRateClient
     */
    public function client($userAgent = self::USER_AGENT, $fallbackValue = 0)
    {
        $this->sort();
        return new RequestRateClient($this->base, $userAgent, $this->requestRates, $fallbackValue);
    }

    /**
     * Sort
     *
     * @return void
     */
    private function sort()
    {
        usort($this->requestRates, function ($a, $b) {
            // PHP 7: Switch to the <=> "Spaceship" operator
            return $b['rate'] > $a['rate'];
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
