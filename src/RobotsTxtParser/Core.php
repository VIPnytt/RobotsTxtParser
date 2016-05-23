<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Core\Directives\CleanParam;
use vipnytt\RobotsTxtParser\Core\Directives\Host;
use vipnytt\RobotsTxtParser\Core\Directives\Sitemap;
use vipnytt\RobotsTxtParser\Core\Directives\UserAgent;
use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Core\Toolbox;

/**
 * Class Core
 *
 * @package vipnytt\RobotsTxtParser
 */
class Core implements RobotsTxtInterface
{
    use Toolbox;

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
     * Previous directive
     * @var string
     */
    protected $previousDirective;

    /**
     * Current user-agent(s)
     * @var array
     */
    protected $userAgentValues;

    /**
     * Clean-param class
     * @var CleanParam
     */
    protected $cleanParam;

    /**
     * Host class
     * @var Host
     */
    protected $host;

    /**
     * Sitemap class
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * User-agent class
     * @var UserAgent
     */
    protected $userAgent;

    /**
     * Core constructor.
     *
     * @param string $content - file content
     */
    public function __construct($content)
    {
        mb_internal_encoding(self::ENCODING);
        $this->cleanParam = new CleanParam();
        $this->host = new Host();
        $this->sitemap = new Sitemap();
        $this->userAgent = new UserAgent();
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
                $this->userAgentValues = [];
            }
            $this->userAgentValues[] = $pair['value'];
        }
        $this->previousDirective = $pair['directive'];
        switch ($pair['directive']) {
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->cleanParam->add($pair['value']);
            case self::DIRECTIVE_HOST:
                return $this->host->add($pair['value']);
            case self::DIRECTIVE_SITEMAP:
                return $this->sitemap->add($pair['value']);
            case self::DIRECTIVE_USER_AGENT:
                return $this->userAgent->set($this->userAgentValues);
        }
        return $this->userAgent->add($line);
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
            $this->cleanParam->render(),
            $this->host->render(),
            $this->sitemap->render(),
            $this->userAgent->render()
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
            $this->cleanParam->export(),
            $this->host->export(),
            $this->sitemap->export(),
            $this->userAgent->export()
        );
    }
}
