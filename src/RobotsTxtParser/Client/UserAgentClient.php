<?php
namespace vipnytt\RobotsTxtParser\Client;

use vipnytt\RobotsTxtParser\Core\Directives\UserAgent;
use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Core\StatusCodeParser;
use vipnytt\RobotsTxtParser\Core\UrlParser;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\UserAgentParser;

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
     * @param string $userAgent
     * @param UserAgent $rules
     * @param string $baseUri
     * @param int|null $statusCode
     */
    public function __construct($userAgent, UserAgent $rules, $baseUri, $statusCode)
    {
        $this->statusCodeParser = new StatusCodeParser($statusCode, parse_url($baseUri, PHP_URL_SCHEME));
        $this->rules = $rules;
        $this->base = $baseUri;
        $userAgentParser = new UserAgentParser(mb_strtolower($userAgent));
        if (($this->userAgent = $userAgentParser->match($rules->userAgents)) === false) {
            $this->userAgent = self::USER_AGENT;
        }
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
        foreach (
            [
                self::DIRECTIVE_DISALLOW => $this->rules->disallow[$this->userAgent],
                self::DIRECTIVE_ALLOW => $this->rules->allow[$this->userAgent]
            ] as $currentDirective => $currentRules
        ) {
            if ($currentRules->check($url)) {
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
        $delay = $this->rules->cacheDelay[$this->userAgent]->export();
        return isset($delay[self::DIRECTIVE_CACHE_DELAY]) ? $delay[self::DIRECTIVE_CACHE_DELAY] : $this->getCrawlDelay();
    }

    /**
     * Get Crawl-delay
     *
     * @return float|int
     */
    public function getCrawlDelay()
    {
        $delay = $this->rules->crawlDelay[$this->userAgent]->export();
        return isset($delay[self::DIRECTIVE_CRAWL_DELAY]) ? $delay[self::DIRECTIVE_CRAWL_DELAY] : $this->getRequestRate();
    }

    /**
     * Get Request-rate for current timestamp
     *
     * @param int|null $timestamp
     * @return float|int
     */
    protected function getRequestRate($timestamp = null)
    {
        $values = $this->determineRequestRates(is_int($timestamp) ? $timestamp : time());
        if (
            count($values) > 0 &&
            ($rate = min($values)) > 0
        ) {
            return $rate;
        }
        return 0;
    }

    /**
     * Determine Request rates
     *
     * @param $timestamp
     * @return array
     */
    protected function determineRequestRates($timestamp)
    {
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
        }
        return $values;
    }

    /**
     * Get Request-rates
     *
     * @return array
     */
    public function getRequestRates()
    {
        $array = $this->rules->requestRate[$this->userAgent]->export();
        return isset($array[self::DIRECTIVE_REQUEST_RATE]) ? $array[self::DIRECTIVE_REQUEST_RATE] : [];
    }

    /**
     * Rule export
     *
     * @return array
     */
    public function export()
    {
        return array_merge(
            $this->rules->allow[$this->userAgent]->export(),
            $this->rules->comment[$this->userAgent]->export(),
            $this->rules->cacheDelay[$this->userAgent]->export(),
            $this->rules->crawlDelay[$this->userAgent]->export(),
            $this->rules->disallow[$this->userAgent]->export(),
            $this->rules->requestRate[$this->userAgent]->export(),
            $this->rules->robotVersion[$this->userAgent]->export(),
            $this->rules->visitTime[$this->userAgent]->export()
        );
    }

    /**
     * UserAgentClient destructor.
     */
    public function __destruct()
    {
        if (!$this->commentsExported) {
            // Comment from the `Comments` directive exists, but has not been read.
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
        $comments = $this->rules->comment[$this->userAgent]->export();
        return isset($comments[self::DIRECTIVE_COMMENT]) ? $comments[self::DIRECTIVE_COMMENT] : [];
    }

    /**
     * Get Visit-time
     *
     * @return array|false
     */
    public function getVisitTime()
    {
        $times = $this->rules->visitTime[$this->userAgent]->export();
        return isset($times[self::DIRECTIVE_VISIT_TIME]) ? $times[self::DIRECTIVE_VISIT_TIME] : [];
    }
}
