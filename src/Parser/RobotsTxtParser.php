<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\Parser\Directives\RootDirectiveHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RobotsTxtParser
 *
 * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt
 * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml
 * @link http://www.robotstxt.org/robotstxt.html
 * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
 * @link http://www.conman.org/people/spc/robots2.html
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
class RobotsTxtParser implements RobotsTxtInterface
{
    use DirectiveParserCommons;
    use UrlParser;

    /**
     * Directive white list
     */
    const TOP_LEVEL_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM => 'cleanParam',
        self::DIRECTIVE_HOST => 'host',
        self::DIRECTIVE_SITEMAP => 'sitemap',
        self::DIRECTIVE_USER_AGENT => 'userAgent',
    ];

    /**
     * Root directive handler
     * @var RootDirectiveHandler
     */
    protected $handler;

    /**
     * Current user-agent(s)
     * @var array
     */
    private $userAgents;

    /**
     * Previous directive
     * @var string
     */
    private $previousDirective;

    /**
     * Basic constructor.
     *
     * @param string $baseUri
     * @param string $content
     */
    public function __construct($baseUri, $content)
    {
        mb_internal_encoding(self::ENCODING);
        $this->handler = new RootDirectiveHandler($this->urlBase($this->urlEncode($baseUri)));
        $this->parseTxt($content);
    }

    /**
     * Client robots.txt
     *
     * @param string $txt
     * @return void
     */
    private function parseTxt($txt)
    {
        $lines = array_filter(array_map('trim', mb_split('\r\n|\n|\r', $txt)));
        // Client each line individually
        foreach ($lines as $line) {
            // Limit rule length
            $line = mb_substr($line, 0, self::MAX_LENGTH_RULE);
            // Remove comments
            $line = mb_split('#', $line, 2)[0];
            // Client line
            $this->add($line);
        }
    }

    /**
     * Add line
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $previousDirective = $this->previousDirective;
        $pair = $this->generateRulePair($line, array_keys(self::TOP_LEVEL_DIRECTIVES));
        if ($pair === false) {
            $this->previousDirective = $line;
            return $this->handler->userAgent()->add($line);
        } elseif ($pair['directive'] === self::DIRECTIVE_USER_AGENT) {
            if ($previousDirective !== self::DIRECTIVE_USER_AGENT) {
                $this->userAgents = [];
            }
            $this->userAgents[] = $pair['value'];
            $this->previousDirective = $pair['directive'];
            return $this->handler->userAgent()->set($this->userAgents);
        }
        $this->previousDirective = $pair['directive'];
        return $this->handler->{self::TOP_LEVEL_DIRECTIVES[$pair['directive']]}()->add($pair['value']);
    }

    /**
     * Render
     *
     * @param string $lineSeparator
     * @return string
     */
    public function render($lineSeparator = "\n")
    {
        return implode($lineSeparator, array_merge(
            $this->handler->host()->render(),
            $this->handler->cleanParam()->render(),
            $this->handler->sitemap()->render(),
            $this->handler->userAgent()->render()
        ));
    }

    /**
     * Rule array
     *
     * @return array
     */
    public function export()
    {
        return [
            self::DIRECTIVE_HOST => $this->handler->host()->client()->export(),
            self::DIRECTIVE_CLEAN_PARAM => $this->handler->cleanParam()->client()->export(),
            self::DIRECTIVE_SITEMAP => $this->handler->sitemap()->client()->export(),
            self::DIRECTIVE_USER_AGENT => $this->handler->userAgent()->export(),
        ];
    }
}
