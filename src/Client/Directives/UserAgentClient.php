<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\Directives\SubDirectiveHandler;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

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
     * @var SubDirectiveHandler
     */
    private $handler;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * Status code
     * @var int|null
     */
    private $statusCode;

    /**
     * UserAgentClient constructor.
     *
     * @param SubDirectiveHandler $handler
     * @param string $baseUri
     * @param int|null $statusCode
     */
    public function __construct(SubDirectiveHandler $handler, $baseUri, $statusCode)
    {
        $this->handler = $handler;
        $this->base = $baseUri;
        $this->statusCode = $statusCode;
    }

    /**
     * UserAgentClient destructor.
     */
    public function __destruct()
    {
        $this->comment();
    }

    /**
     * Comment
     *
     * @return CommentClient
     */
    public function comment()
    {
        return $this->handler->comment()->client();
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
        $statusCodeParser = new StatusCodeParser($this->statusCode, parse_url($this->base, PHP_URL_SCHEME));
        $statusCodeParser->replaceUnofficial();
        if (($result = $statusCodeParser->check()) !== null) {
            return $directive === $result;
        }
        $result = self::DIRECTIVE_ALLOW;
        foreach (
            [
                self::DIRECTIVE_DISALLOW => $this->handler->disallow(),
                self::DIRECTIVE_ALLOW => $this->handler->allow()
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
     * @param string[] $urls
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
        return $this->handler->cacheDelay()->client($this->crawlDelay()->get());
    }

    /**
     * Crawl-delay
     *
     * @return DelayClient
     */
    public function crawlDelay()
    {
        return $this->handler->crawlDelay()->client($this->requestRate()->get());
    }

    /**
     * RequestClient-rate
     *
     * @return RequestRateClient
     */
    public function requestRate()
    {
        return $this->handler->requestRate()->client();
    }

    /**
     * Robot-version
     *
     * @return RobotVersionClient
     */
    public function robotVersion()
    {
        return $this->handler->robotVersion()->client();
    }

    /**
     * Rule export
     *
     * @return array
     */
    public function getRules()
    {
        return array_merge(
            $this->handler->allow()->getRules(),
            $this->handler->comment()->getRules(),
            $this->handler->cacheDelay()->getRules(),
            $this->handler->crawlDelay()->getRules(),
            $this->handler->disallow()->getRules(),
            $this->handler->requestRate()->getRules(),
            $this->handler->robotVersion()->getRules(),
            $this->handler->visitTime()->getRules()
        );
    }

    /**
     * Visit-time
     *
     * @return VisitTimeClient
     */
    public function visitTime()
    {
        return $this->handler->visitTime()->client();
    }
}
