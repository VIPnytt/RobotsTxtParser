<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Directives\CleanParam;
use vipnytt\RobotsTxtParser\Directives\Host;
use vipnytt\RobotsTxtParser\Directives\Sitemap;
use vipnytt\RobotsTxtParser\Directives\UserAgent;

abstract class Core implements RobotsTxtInterface
{
    use ObjectTools;

    const TOP_LEVEL_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM,
        self::DIRECTIVE_HOST,
        self::DIRECTIVE_SITEMAP,
        self::DIRECTIVE_USER_AGENT,
    ];

    protected $raw;

    protected $previousDirective;
    protected $userAgentValues;

    protected $cleanParam;
    protected $host;
    protected $sitemap;
    protected $userAgent;

    /**
     * Constructor
     *
     * @param string $content - file content
     * @param string $encoding - character encoding
     * @param integer|null $byteLimit - maximum of bytes to parse
     * @throws Exceptions\ParserException
     */
    public function __construct($content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        if (!mb_internal_encoding($encoding)) {
            throw new Exceptions\ParserException('Unable to set internal character encoding to `' . $encoding . '`');
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

    public function export()
    {
        return $this->cleanParam->export()
        + $this->host->export()
        + $this->sitemap->export()
        + $this->userAgent->export();
    }
}
