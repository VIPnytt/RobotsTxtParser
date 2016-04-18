<?php
namespace vipnytt\RobotsTxtParser\Modules;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Modules\Directives\DisAllow;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class UserAgentClient
 *
 * @package vipnytt\RobotsTxtParser\Modules
 */
class UserAgentClient implements RobotsTxtInterface
{
    /**
     * Allow rules
     * @var DisAllow
     */
    protected $allow;

    /**
     * Disallow rules
     * @var DisAllow
     */
    protected $disallow;

    /**
     * User-agent
     * @var string
     */
    protected $userAgent;

    /**
     * Robots.txt origin
     * @var string
     */
    protected $origin;

    /**
     * Status code parser
     * @var StatusCodeParser
     */
    protected $statusCodeParser;

    /**
     * UserAgentClient constructor.
     *
     * @param DisAllow $allow
     * @param DisAllow $disallow
     * @param string $userAgent
     * @param string $origin
     * @param int $statusCode
     */
    public function __construct($allow, $disallow, $userAgent, $origin, $statusCode)
    {
        $this->statusCodeParser = new StatusCodeParser($statusCode, parse_url($origin, PHP_URL_SCHEME));
        $this->userAgent = $userAgent;
        $this->origin = $origin;
        $this->allow = $allow;
        $this->disallow = $disallow;
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
        if (!$this->isUrlApplicable([$url, $this->origin])) {
            throw new ClientException('URL belongs to a different robots.txt, please check it against that one instead');
        }
        $this->statusCodeParser->replaceUnofficial();
        if (($result = $this->statusCodeParser->check()) !== null) {
            return $directive === $result;
        }
        $result = self::DIRECTIVE_ALLOW;
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $currentDirective) {
            if ($this->$currentDirective->check($url)) {
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
        $exported = $this->{self::DIRECTIVE_CACHE_DELAY}->export();
        return isset($exported[self::DIRECTIVE_CACHE_DELAY]) ? $exported[self::DIRECTIVE_CACHE_DELAY] : $this->getCrawlDelay();
    }

    /**
     * Get Crawl-delay
     *
     * @return float|int
     */
    public function getCrawlDelay()
    {
        $exported = $this->{self::DIRECTIVE_CRAWL_DELAY}->export();
        return isset($exported[self::DIRECTIVE_CRAWL_DELAY]) ? $exported[self::DIRECTIVE_CRAWL_DELAY] : 0;
    }
}
