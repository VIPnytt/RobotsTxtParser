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
        $unit = strtolower(substr(preg_replace('/[^A-Za-z]/', '', filter_var($parts[1], FILTER_SANITIZE_STRING)), 0, 1));
        $multiplier = isset($this->units[$unit]) ? $this->units[$unit] : 1;
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
                // PHP 7: Switch to the <=> "Spaceship" operator
                return $requestRateB['rate'] > $requestRateA['rate'];
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
            $multiplyFactor = $this->decimalMultiplier($array['rate']);
            $multipliedRate = $array['rate'] * $multiplyFactor;
            $gcd = $this->getGCD($multiplyFactor, $multipliedRate);
            $requests = $multiplyFactor / $gcd;
            $time = $multipliedRate / $gcd;
            $suffix = 's';
            foreach ($this->units as $unit => $sec) {
                if ($time % $sec === 0) {
                    $suffix = $unit;
                    $time /= $sec;
                    break;
                }
            }
            if (isset($array['from']) &&
                isset($array['to'])
            ) {
                $suffix .= ' ' . $array['from'] . '-' . $array['to'];
            }
            $handler->add(self::DIRECTIVE_REQUEST_RATE, $requests . '/' . $time . $suffix);
        }
        return true;
    }

    /**
     * @param int|float $value
     * @return int
     */
    private function decimalMultiplier($value)
    {
        $multiplier = 1;
        while (fmod($value, 1) != 0) {
            $value *= 10;
            $multiplier *= 10;
        }
        return $multiplier;
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
}
