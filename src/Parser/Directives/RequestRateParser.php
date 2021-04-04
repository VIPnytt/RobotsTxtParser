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
     * Sorted
     * @var bool
     */
    private $sorted = false;

    /**
     * Time units
     * @var int[]
     */
    private $units = [
        'w' => 604800,
        'd' => 86400,
        'h' => 3600,
        'm' => 60,
    ];

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
        $parts = array_map('trim', explode('/', $array[0]));
        if (count($parts) != 2) {
            return false;
        }
        $unit = strtolower(substr(preg_replace('/[^A-Za-z]/', '', filter_var($parts[1], FILTER_SANITIZE_STRING)), 0, 1));
        $multiplier = isset($this->units[$unit]) ? $this->units[$unit] : 1;

        $rate = (int)abs(filter_var($parts[0], FILTER_SANITIZE_NUMBER_INT));
        $time = $multiplier * (int)abs(filter_var($parts[1], FILTER_SANITIZE_NUMBER_INT));

        $result = [
            'rate' => $time / $rate,
            'ratio' => $this->getRatio($rate, $time),
            'from' => null,
            'to' => null,
        ];
        if (!empty($array[1]) &&
            ($times = $this->draftParseTime($array[1])) !== false
        ) {
            $result = array_merge($result, $times);
        }
        $this->requestRates[] = $result;
        return true;
    }

    /**
     * Get ratio string
     *
     * @param int $rate
     * @param int $time
     * @return string
     */
    private function getRatio($rate, $time)
    {
        $gcd = $this->getGCD($rate, $time);
        $requests = $rate / $gcd;
        $time = $time / $gcd;
        $suffix = 's';
        foreach ($this->units as $unit => $sec) {
            if ($time % $sec === 0) {
                $suffix = $unit;
                $time /= $sec;
                break;
            }
        }
        return $requests . '/' . $time . $suffix;
    }

    /**
     * Returns the greatest common divisor of two integers using the Euclidean algorithm.
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function getGCD($a, $b)
    {
        if (extension_loaded('gmp')) {
            return gmp_intval(gmp_gcd((string)$a, (string)$b));
        }
        $large = $a > $b ? $a : $b;
        $small = $a > $b ? $b : $a;
        $remainder = $large % $small;
        return 0 === $remainder ? $small : $this->getGCD($small, $remainder);
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
     * @return bool
     */
    private function sort()
    {
        if (!$this->sorted) {
            $this->sorted = true;
            return usort($this->requestRates, function (array $requestRateA, array $requestRateB) {
                return $requestRateB['rate'] <=> $requestRateA['rate'];
            });
        }
        return $this->sorted;
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
            $time = '';
            if (isset($array['from']) &&
                isset($array['to'])
            ) {
                $time .= ' ' . $array['from'] . '-' . $array['to'];
            }
            $handler->add(self::DIRECTIVE_REQUEST_RATE, $array['ratio'] . $time);
        }
        return true;
    }
}
