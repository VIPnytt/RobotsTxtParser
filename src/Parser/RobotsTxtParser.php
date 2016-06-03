<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\Parser\Directives\RootDirectiveHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class Core
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
        self::DIRECTIVE_CLEAN_PARAM,
        self::DIRECTIVE_HOST,
        self::DIRECTIVE_SITEMAP,
        self::DIRECTIVE_USER_AGENT,
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
     * Core constructor.
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
        $pair = $this->generateRulePair($line, self::TOP_LEVEL_DIRECTIVES);
        if ($pair['directive'] === self::DIRECTIVE_USER_AGENT) {
            if ($previousDirective !== self::DIRECTIVE_USER_AGENT) {
                $this->userAgents = [];
            }
            $this->userAgents[] = $pair['value'];
        }
        $this->previousDirective = $pair['directive'];
        switch ($pair['directive']) {
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->handler->cleanParam()->add($pair['value']);
            case self::DIRECTIVE_HOST:
                return $this->handler->host()->add($pair['value']);
            case self::DIRECTIVE_SITEMAP:
                return $this->handler->sitemap()->add($pair['value']);
            case self::DIRECTIVE_USER_AGENT:
                return $this->handler->userAgent()->set($this->userAgents);
        }
        return $this->handler->userAgent()->add($line);
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
     * Export rules
     *
     * @return array
     */
    public function export()
    {
        return array_merge(
            $this->handler->host()->getRules(),
            $this->handler->cleanParam()->getRules(),
            $this->handler->sitemap()->getRules(),
            $this->handler->userAgent()->getRules()
        );
    }
}
