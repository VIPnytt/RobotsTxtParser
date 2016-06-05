<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\Parser\Directives\SubDirectiveHandler;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

class UserAgentTools implements RobotsTxtInterface
{
    use UrlParser;
    use DirectiveParserCommons;

    /**
     * Rules
     * @var SubDirectiveHandler
     */
    protected $handler;

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
     * @param string $url
     * @return bool
     * @throws ClientException
     */
    private function check($directive, $url)
    {
        $directive = $this->validateDirective($directive, [self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW]);
        $url = $this->urlConvertToFull($url, $this->base);
        if (!$this->isUrlApplicable([$url, $this->base])) {
            throw new ClientException('URL belongs to a different robots.txt');
        }
        $statusCodeParser = new StatusCodeParser($this->statusCode, parse_url($this->base, PHP_URL_SCHEME));
        $statusCodeParser->codeOverride();
        if (($result = $statusCodeParser->accessOverrideCheck()) !== null) {
            return $directive === $result;
        }
        if ($this->handler->visitTime()->client()->isVisitTime() === false) {
            return $result === self::DIRECTIVE_DISALLOW;
        }
        $result = self::DIRECTIVE_ALLOW;
        foreach (
            [
                self::DIRECTIVE_DISALLOW => $this->handler->disallow(),
                self::DIRECTIVE_ALLOW => $this->handler->allow(),
            ] as $currentDirective => $ruleClient
        ) {
            if ($ruleClient->client()->isListed($url)) {
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
     * Rule export
     *
     * @return array
     */
    public function export()
    {
        return [
            self::DIRECTIVE_ROBOT_VERSION => $this->handler->robotVersion()->client()->export(),
            self::DIRECTIVE_VISIT_TIME => $this->handler->visitTime()->client()->export(),
            self::DIRECTIVE_DISALLOW => $this->handler->disallow()->client()->export(),
            self::DIRECTIVE_ALLOW => $this->handler->allow()->client()->export(),
            self::DIRECTIVE_CRAWL_DELAY => $this->handler->crawlDelay()->client()->export(),
            self::DIRECTIVE_CACHE_DELAY => $this->handler->cacheDelay()->client()->export(),
            self::DIRECTIVE_REQUEST_RATE => $this->handler->requestRate()->client()->export(),
            self::DIRECTIVE_COMMENT => $this->handler->comment()->client()->export(),
        ];
    }
}
