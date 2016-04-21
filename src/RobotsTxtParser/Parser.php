<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions\EncodingException;
use vipnytt\RobotsTxtParser\Parser\Directives\CleanParam;
use vipnytt\RobotsTxtParser\Parser\Directives\Host;
use vipnytt\RobotsTxtParser\Parser\Directives\Sitemap;
use vipnytt\RobotsTxtParser\Parser\Directives\UserAgent;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Parser\Toolbox;

/**
 * Class Core
 *
 * @package vipnytt\RobotsTxtParser
 */
abstract class Parser implements RobotsTxtInterface
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
     * @param string $encoding - character encoding
     * @param int|null $byteLimit - maximum of bytes to parse
     * @throws EncodingException
     */
    public function __construct($content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        try {
            if (!mb_internal_encoding($encoding)) {
                throw new EncodingException('Unable to set internal character encoding to ' . $encoding);
            }
        } catch (\Exception $e) {
            throw new EncodingException($e);
        }
        $this->cleanParam = new CleanParam();
        $this->host = new Host();
        $this->sitemap = new Sitemap();
        $this->userAgent = new UserAgent();
        if (is_int($byteLimit) && $byteLimit > 0) {
            $content = mb_strcut($content, 0, $byteLimit);
        }
        $this->parseTxt($content);
    }

    /**
     * Parse robots.txt
     *
     * @param string $txt
     * @return void
     */
    private function parseTxt($txt)
    {
        $lines = array_filter(array_map('trim', mb_split('\r\n|\n|\r', $txt)));
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
