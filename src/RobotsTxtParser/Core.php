<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;
use vipnytt\RobotsTxtParser\Modules\Directives\CleanParam;
use vipnytt\RobotsTxtParser\Modules\Directives\Host;
use vipnytt\RobotsTxtParser\Modules\Directives\Sitemap;
use vipnytt\RobotsTxtParser\Modules\Directives\UserAgent;
use vipnytt\RobotsTxtParser\Modules\Toolbox;

/**
 * Class Core
 *
 * @package vipnytt\RobotsTxtParser
 */
abstract class Core implements RobotsTxtInterface
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
     * RAW robots.txt content
     * @var string
     */
    protected $raw;

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
     * @param string $encoding - character encoding
     * @param integer|null $byteLimit - maximum of bytes to parse
     * @throws ParserException
     */
    public function __construct($content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        if (!mb_internal_encoding($encoding)) {
            throw new ParserException('Unable to set internal character encoding to `' . $encoding . '`');
        }
        $this->cleanParam = new CleanParam();
        $this->host = new Host();
        $this->sitemap = new Sitemap();
        $this->userAgent = new UserAgent();

        $this->raw = is_int($byteLimit) ? mb_strcut($content, 0, $byteLimit, $encoding) : $content;
        $this->parseTxt();
    }

    /**
     * Parse robots.txt
     *
     * @return void
     */
    private function parseTxt()
    {
        $lines = array_filter(array_map('trim', mb_split('\r\n|\n|\r', $this->raw)));
        // Parse each line individually
        foreach ($lines as $line) {
            // Limit rule length
            $line = mb_substr($line, 0, self::MAX_LENGTH_RULE);
            // Remove comments
            $line = mb_split('#', $line, 2)[0];
            // Parse line
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
     * Export
     *
     * @return array
     */
    public function export()
    {
        return $this->cleanParam->export()
        + $this->host->export()
        + $this->sitemap->export()
        + $this->userAgent->export();
    }
}
