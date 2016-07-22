<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Handler\Directives\RootDirectiveHandler;
use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RobotsTxtParser
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
class RobotsTxtParser implements RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Directive white list
     */
    const TOP_LEVEL_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM => 'cleanParam',
        self::DIRECTIVE_HOST => 'host',
        self::DIRECTIVE_SITEMAP => 'sitemap',
    ];

    /**
     * Root directive handler
     * @var RootDirectiveHandler
     */
    protected $handler;

    /**
     * TxtClient constructor.
     *
     * @param string $baseUri
     * @param string $content
     * @param string|null $effectiveUri
     */
    public function __construct($baseUri, $content, $effectiveUri = null)
    {
        mb_internal_encoding(self::ENCODING);
        $baseParser = new UriParser($baseUri);
        $baseUri = $baseParser->base();
        $effectiveBase = $baseUri;
        if ($effectiveUri !== null) {
            $effectiveParser = new UriParser($effectiveUri);
            $effectiveBase = $effectiveParser->base();
        }
        $this->handler = new RootDirectiveHandler($baseUri, $effectiveBase);
        $this->parseTxt($content);
    }

    /**
     * Client robots.txt
     *
     * @param string $txt
     * @return bool
     */
    private function parseTxt($txt)
    {
        $result = [];
        $lines = array_filter(array_map('trim', mb_split('\r\n|\n|\r', $txt)));
        // Client each line individually
        foreach ($lines as $line) {
            // Limit rule length
            $line = mb_substr($line, 0, self::MAX_LENGTH_RULE);
            // Remove comments
            $line = explode('#', $line, 2)[0];
            // Parse line
            $result[] = $this->parseLine($line);
        }
        return in_array(true, $result, true);
    }

    /**
     * Add line
     *
     * @param string $line
     * @return bool
     */
    private function parseLine($line)
    {
        if (($pair = $this->generateRulePair($line, array_keys(self::TOP_LEVEL_DIRECTIVES))) !== false) {
            return $this->handler->{self::TOP_LEVEL_DIRECTIVES[$pair['directive']]}()->add($pair['value']);
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
