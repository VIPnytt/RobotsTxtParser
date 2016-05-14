<?php
namespace vipnytt\RobotsTxtParser\Client;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class UserAgentClient
 *
 * @package vipnytt\RobotsTxtParser\Client
 */
class UserAgentClient implements RobotsTxtInterface
{
    use UrlParser;

    /**
     * Rules
     * @var array
     */
    protected $rules;

    /**
     * User-agent
     * @var string
     */
    protected $userAgent;

    /**
     * Robots.txt base URL
     * @var string
     */
    protected $base;

    /**
     * Status code parser
     * @var StatusCodeParser
     */
    protected $statusCodeParser;

    /**
     * Comment export status
     * @var bool
     */
    protected $commentsExported = false;

    /**
     * UserAgentClient constructor.
     *
     * @param array $rules
     * @param string $userAgent
     * @param string $baseUrl
     * @param int|null $statusCode
     */
    public function __construct(array $rules, $userAgent, $baseUrl, $statusCode)
    {
        $this->statusCodeParser = new StatusCodeParser($statusCode, parse_url($baseUrl, PHP_URL_SCHEME));
        $this->userAgent = $userAgent;
        $this->rules = $rules;
        $this->base = $baseUrl;
    }

    /**
     * Check if URL is allowed to crawl
     *
     * @param string $url
     * @return bool
     */
    public function isAllowed($url)
    {
        return $this->check(self::DIRECTIVE_ALLOW, $url);
    }

    /**
     * Check
     *
     * @param string $directive
     * @param string $url - URL to check
     * @return bool
     * @throws ClientException
     */
    protected function check($directive, $url)
    {
        $url = $this->urlConvertToFull($url, $this->base);
        if (!$this->isUrlApplicable([$url, $this->base])) {
            throw new ClientException('URL belongs to a different robots.txt, please check it against that one instead');
        }
        $this->statusCodeParser->replaceUnofficial();
        if (($result = $this->statusCodeParser->check()) !== null) {
            return $directive === $result;
        }
        $result = self::DIRECTIVE_ALLOW;
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $currentDirective) {
            if ($this->rules[$currentDirective]->check($url)) {
                $result = $currentDirective;
            }
        }
        return $directive === $result;
    }

    /**
     * Check if the URL belongs to current robots.txt
     *
     * @param $urls
     * @return bool
     */
    protected function isUrlApplicable($urls)
    {
        foreach ($urls as $url) {
            $parsed = parse_url($url);
            $parsed['port'] = is_int($port = parse_url($url, PHP_URL_PORT)) ? $port : getservbyname($parsed['scheme'], 'tcp');
            $assembled = $parsed['scheme'] . '://' . $parsed['host'] . ':' . $parsed['port'];
            if (!isset($result)) {
                $result = $assembled;
            } elseif ($result !== $assembled) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if URL is disallowed to crawl
     *
     * @param string $url
     * @return bool
     */
    public function isDisallowed($url)
    {
        return $this->check(self::DIRECTIVE_DISALLOW, $url);
    }

    /**
     * Get Cache-delay
     *
     * @return float|int
     */
    public function getCacheDelay()
    {
        $delay = $this->rules[self::DIRECTIVE_CACHE_DELAY]->export();
        return isset($delay[self::DIRECTIVE_CACHE_DELAY]) ? $delay[self::DIRECTIVE_CACHE_DELAY] : $this->getCrawlDelay();
    }

    /**
     * Get Crawl-delay
     *
     * @return float|int
     */
    public function getCrawlDelay()
    {
        $delay = $this->rules[self::DIRECTIVE_CRAWL_DELAY]->export();
        return isset($delay[self::DIRECTIVE_CRAWL_DELAY]) ? $delay[self::DIRECTIVE_CRAWL_DELAY] : $this->getRequestRate();
    }

    /**
     * Get Request-rate
     *
     * @param int|null $timestamp
     * @return float|int
     */
    protected function getRequestRate($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        $rates = $this->getRequestRates();
        $values = [];
        foreach ($rates as $array) {
            if (
                !isset($array['from']) ||
                !isset($array['to'])
            ) {
                $values[] = $array['rate'];
                continue;
            }
            $fromTime = gmmktime(mb_substr($array['from'], 0, mb_strlen($array['from']) - 2), mb_substr($array['from'], -2, 2), 0);
            $toTime = gmmktime(mb_substr($array['to'], 0, mb_strlen($array['to']) - 2), mb_substr($array['to'], -2, 2), 59);
            if ($fromTime > $toTime) {
                $toTime = gmmktime(mb_substr($array['to'] + 24, 0, mb_strlen($array['to']) - 2), mb_substr($array['to'], -2, 2), 59);
            }
            if (
                $timestamp >= $fromTime &&
                $timestamp <= $toTime
            ) {
                $values[] = $array['rate'];
            }
        };
        if (
            count($values) > 0 &&
            ($rate = min($values)) > 0
        ) {
            return $rate;
        }
        return 0;
    }

    /**
     * Get Request-rates
     *
     * @return array
     */
    public function getRequestRates()
    {
        $array = $this->rules[self::DIRECTIVE_REQUEST_RATE]->export();
        return isset($array[self::DIRECTIVE_REQUEST_RATE]) ? $array[self::DIRECTIVE_REQUEST_RATE] : [];
    }

    /**
     * Rule export
     *
     * @return array
     */
    public function export()
    {
        $result = [];
        foreach ($this->rules as $directive => $object) {
            if (!empty($export = $object->export())) {
                $result[$directive] = $export[$directive];
            }
        }
        return $result;
    }

    /**
     * UserAgentClient destructor.
     */
    public function __destruct()
    {
        if (!$this->commentsExported) {
            // Comment from the `Comments` directive exists, but has not been exported.
            foreach ($this->getComments() as $message) {
                trigger_error('Comment for `' . $this->userAgent . '` at `' . $this->base . '/robots.txt`: ' . $message, E_USER_NOTICE);
            }
        }
    }

    /**
     * Get Comments
     *
     * @return array
     */
    public function getComments()
    {
        $this->commentsExported = true;
        $comments = $this->rules[self::DIRECTIVE_COMMENT]->export();
        return isset($comments[self::DIRECTIVE_COMMENT]) ? $comments[self::DIRECTIVE_COMMENT] : [];
    }

    /**
     * Get Visit-time
     *
     * @return array|false
     */
    public function getVisitTime()
    {
        $times = $this->rules[self::DIRECTIVE_VISIT_TIME]->export();
        return isset($times[self::DIRECTIVE_VISIT_TIME]) ? $times[self::DIRECTIVE_VISIT_TIME] : [];
    }
}
