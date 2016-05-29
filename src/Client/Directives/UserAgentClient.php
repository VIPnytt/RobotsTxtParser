<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\Directives\UserAgentParser;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\UserAgentParser as UAStringParser;

/**
 * Class UserAgentClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class UserAgentClient implements RobotsTxtInterface
{
    use UrlParser;

    /**
     * Rules
     * @var array
     */
    private $rules;

    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * Origin user-agent
     * @var string
     */
    private $userAgentOrigin;

    /**
     * Robots.txt base URL
     * @var string
     */
    private $base;

    /**
     * Status code parser
     * @var StatusCodeParser
     */
    private $statusCodeParser;

    /**
     * Comment export status
     * @var bool
     */
    private $commentsExported = false;

    /**
     * UserAgentClient constructor.
     *
     * @param string $userAgent
     * @param UserAgentParser $rules
     * @param string $baseUri
     * @param int|null $statusCode
     */
    public function __construct($userAgent, UserAgentParser $rules, $baseUri, $statusCode)
    {
        $this->statusCodeParser = new StatusCodeParser($statusCode, parse_url($baseUri, PHP_URL_SCHEME));
        $this->rules = $rules;
        $this->base = $baseUri;
        $this->userAgentOrigin = mb_strtolower($userAgent);
        $userAgentParser = new UAStringParser($this->userAgentOrigin);
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
    private function check($directive, $url)
    {
        $url = $this->urlConvertToFull($url, $this->base);
        if (!$this->isUrlApplicable([$url, $this->base])) {
            throw new ClientException('URL belongs to a different robots.txt');
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
    private function isUrlApplicable($urls)
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
     * Cache-delay
     *
     * @return DelayClient
     */
    public function cacheDelay()
    {
        $export = $this->rules->cacheDelay[$this->userAgent]->export();
        $delay = isset($export[self::DIRECTIVE_CACHE_DELAY]) ? $export[self::DIRECTIVE_CACHE_DELAY] : 0;
        return new DelayClient($this->base, $this->userAgent, $delay, $this->crawlDelay()->get());
    }

    /**
     * Crawl-delay
     *
     * @return DelayClient
     */
    public function crawlDelay()
    {
        $export = $this->rules->crawlDelay[$this->userAgent]->export();
        $delay = isset($export[self::DIRECTIVE_CRAWL_DELAY]) ? $export[self::DIRECTIVE_CRAWL_DELAY] : 0;
        return new DelayClient($this->base, $this->userAgent, $delay, $this->requestRate()->get());
    }

    /**
     * RequestClient-rate
     *
     * @return RequestRateClient
     */
    public function requestRate()
    {
        $array = $this->rules->requestRate[$this->userAgent]->export();
        $rates = isset($array[self::DIRECTIVE_REQUEST_RATE]) ? $array[self::DIRECTIVE_REQUEST_RATE] : [];
        return new RequestRateClient($this->base, $this->userAgent, $rates);
    }

    /**
     * Robot-version
     *
     * @return RobotVersionClient
     */
    public function robotVersion()
    {
        $export = $this->rules->robotVersion[$this->userAgent]->export();
        return new RobotVersionClient(isset($export[self::DIRECTIVE_ROBOT_VERSION]) ? $export[self::DIRECTIVE_ROBOT_VERSION] : null);
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
     * Visit-time
     *
     * @return VisitTimeClient
     */
    public function visitTime()
    {
        $export = $this->rules->visitTime[$this->userAgent]->export();
        $times = isset($export[self::DIRECTIVE_VISIT_TIME]) ? $export[self::DIRECTIVE_VISIT_TIME] : [];
        return new VisitTimeClient($times);
    }

    /**
     * UserAgentClient destructor.
     */
    public function __destruct()
    {
        if (!$this->commentsExported && $this->userAgent != self::USER_AGENT) {
            // Comment from the `Comments` directive exists, but has not been read.
            foreach ($this->comment()->export() as $message) {
                trigger_error($this->userAgent . ' @ ' . $this->base . self::PATH . ': ' . $message, E_USER_NOTICE);
            }
        }
    }

    /**
     * Comment
     *
     * @return CommentClient
     */
    public function comment()
    {
        $this->commentsExported = true;
        $export = $this->rules->comment[$this->userAgent]->export();
        $comments = isset($export[self::DIRECTIVE_COMMENT]) ? $export[self::DIRECTIVE_COMMENT] : [];
        return new CommentClient($comments);
    }
}
