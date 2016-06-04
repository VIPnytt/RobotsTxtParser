<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;


use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\Parser\Directives\SubDirectiveHandler;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

class Checks implements RobotsTxtInterface
{
    use UrlParser;
    use DirectiveParserCommons;

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
     * Rules
     * @var SubDirectiveHandler
     */
    private $handler;

    /**
     * DisAllowClient constructor.
     *
     * @param string $base
     * @param int|null $statusCode
     * @param SubDirectiveHandler $handler
     */
    public function __construct($base, $statusCode, SubDirectiveHandler $handler)
    {
        $this->base = $base;
        $this->statusCode = $statusCode;
        $this->handler = $handler;
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
        $directive = $this->validateDirective($directive, [self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW]);
        $url = $this->urlConvertToFull($url, $this->base);
        if (!$this->isUrlApplicable([$url, $this->base])) {
            throw new ClientException('URL belongs to a different robots.txt');
        }
        $statusCodeParser = new StatusCodeParser($this->statusCode, parse_url($this->base, PHP_URL_SCHEME));
        $statusCodeParser->replaceUnofficial();
        if (($result = $statusCodeParser->check()) !== null) {
            return $directive === $result;
        }
        if ($this->handler->visitTime()->client()->isVisitTime() === false) {
            return $result === self::DIRECTIVE_DISALLOW;
        }
        $result = self::DIRECTIVE_ALLOW;
        foreach (
            [
                self::DIRECTIVE_DISALLOW => $this->handler->disallow()->client(),
                self::DIRECTIVE_ALLOW => $this->handler->allow()->client(),
            ] as $currentDirective => $ruleClient
        ) {
            if ($ruleClient->affected($url)) {
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
}
