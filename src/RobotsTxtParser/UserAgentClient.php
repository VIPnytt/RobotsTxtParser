<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Directives\DisAllow;
use vipnytt\RobotsTxtParser\Exceptions\ParserException;

class UserAgentClient implements RobotsTxtInterface
{
    protected $allow;
    protected $disallow;

    protected $userAgent;
    protected $origin;
    protected $statusCodeParser;

    public function __construct($rules, $userAgent, $origin, $statusCode)
    {
        $this->statusCodeParser = new StatusCodeParser($statusCode, parse_url($origin, PHP_URL_SCHEME));
        $this->userAgent = $userAgent;
        $this->origin = $origin;
        $this->validateRules($rules);
    }

    protected function validateRules($rules)
    {
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $directive) {
            if (!$rules[$directive] instanceof DisAllow) {
                throw new ParserException('Invalid rule object');
            }
            $this->$directive = $rules[$directive];
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
     * @throws ParserException
     */
    protected function check($directive, $url)
    {
        //TODO: Throw new exception Cannot check URL, belongs to a different robots.txt
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
     * Get Crawl-delay
     *
     * @return array
     */
    public function getCrawlDelay()
    {
        //TODO: Crawl-delay
    }

    /**
     * Get Cache-delay
     *
     * @return array
     */
    public function getCacheDelay()
    {
        //TODO: Cache-delay
    }
}
