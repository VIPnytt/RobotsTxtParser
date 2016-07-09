<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Handler\Directives\SubDirectiveHandler;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class UserAgentTools
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class UserAgentTools implements RobotsTxtInterface
{
    use UriParser;

    /**
     * Rules
     * @var SubDirectiveHandler
     */
    protected $handler;

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Status code
     * @var int|null
     */
    private $statusCode;

    /**
     * DisAllowClient constructor.
     *
     * @param string $base
     * @param int|null $statusCode
     * @param SubDirectiveHandler $handler
     */
    public function __construct(SubDirectiveHandler $handler, $base, $statusCode)
    {
        $this->handler = $handler;
        $this->base = $base;
        $this->statusCode = $statusCode;
    }

    /**
     * UserAgentClient destructor.
     */
    public function __destruct()
    {
        $this->handler->comment()->client();
    }

    /**
     * Check if URI is allowed to crawl
     *
     * @param string $uri
     * @return bool
     */
    public function isAllowed($uri)
    {
        return $this->check(self::DIRECTIVE_ALLOW, $uri);
    }

    /**
     * Check
     *
     * @param string $directive
     * @param string $uri
     * @return bool
     * @throws ClientException
     */
    private function check($directive, $uri)
    {
        $uri = $this->uriConvertToFull($uri, $this->base);
        if ($this->base !== $this->uriBase($uri)) {
            throw new ClientException('URI belongs to a different robots.txt');
        }
        // 1st priority override: /robots.txt is permanent allowed
        if (parse_url($uri, PHP_URL_PATH) === self::PATH) {
            return $directive === self::DIRECTIVE_ALLOW;
        }
        // 2st priority override: Status code rules
        $statusCodeParser = new StatusCodeParser($this->statusCode, parse_url($this->base, PHP_URL_SCHEME));
        if (($result = $statusCodeParser->accessOverride()) !== false) {
            return $directive === $result;
        }
        // 3rd priority override: Visit times
        if ($this->handler->visitTime()->client()->isVisitTime() === false) {
            return $directive === self::DIRECTIVE_DISALLOW;
        }
        // Path check
        return $this->checkPath($directive, $uri);
    }

    /**
     * Check path
     *
     * @param string $directive
     * @param string $uri
     * @return bool
     */
    private function checkPath($directive, $uri)
    {
        $result = self::DIRECTIVE_ALLOW;
        foreach (
            [
                self::DIRECTIVE_NO_INDEX => $this->handler->noIndex(),
                self::DIRECTIVE_DISALLOW => $this->handler->disallow(),
                self::DIRECTIVE_ALLOW => $this->handler->allow(),
            ] as $currentDirective => $handler
        ) {
            if ($handler->client()->isListed($uri)) {
                if ($currentDirective === self::DIRECTIVE_NO_INDEX) {
                    return $directive === self::DIRECTIVE_DISALLOW;
                }
                $result = $currentDirective;
            }
        }
        return $directive === $result;
    }

    /**
     * Check if URI is disallowed to crawl
     *
     * @param string $uri
     * @return bool
     */
    public function isDisallowed($uri)
    {
        return $this->check(self::DIRECTIVE_DISALLOW, $uri);
    }

    /**
     * Rule export
     *
     * @return array
     */
    public function export()
    {
        return [
            self::DIRECTIVE_ROBOT_VERSION => $this->handler->robotVersion()->client()->export(),
            self::DIRECTIVE_VISIT_TIME => $this->handler->visitTime()->client()->export(),
            self::DIRECTIVE_NO_INDEX => $this->handler->noIndex()->client()->export(),
            self::DIRECTIVE_DISALLOW => $this->handler->disallow()->client()->export(),
            self::DIRECTIVE_ALLOW => $this->handler->allow()->client()->export(),
            self::DIRECTIVE_CRAWL_DELAY => $this->handler->crawlDelay()->client()->export(),
            self::DIRECTIVE_CACHE_DELAY => $this->handler->cacheDelay()->client()->export(),
            self::DIRECTIVE_REQUEST_RATE => $this->handler->requestRate()->client()->export(),
            self::DIRECTIVE_COMMENT => $this->handler->comment()->client()->export(),
        ];
    }
}
