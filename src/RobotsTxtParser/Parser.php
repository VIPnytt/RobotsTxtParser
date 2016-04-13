<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Directives\CleanParam;
use vipnytt\RobotsTxtParser\Directives\Host;
use vipnytt\RobotsTxtParser\Directives\Sitemap;
use vipnytt\RobotsTxtParser\Directives\UserAgent;

class Parser implements RobotsTxtInterface
{
    use ObjectTools;

    const SUB_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM,
        self::DIRECTIVE_HOST,
        self::DIRECTIVE_SITEMAP,
        self::DIRECTIVE_USER_AGENT,
    ];

    protected $raw;

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

        $this->cleanParam = new CleanParam([]);
        $this->host = new Host([]);
        $this->sitemap = new Sitemap([]);
        $this->userAgent = new UserAgent([]);


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
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        switch ($pair['directive']) {
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->cleanParam->add($pair['value']);
            case self::DIRECTIVE_HOST:
                return $this->host->add($pair['value']);
            case self::DIRECTIVE_SITEMAP:
                return $this->sitemap->add($pair['value']);
            case self::DIRECTIVE_USER_AGENT:
                return $this->userAgent->add($pair['value']);
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

    /**
     * Check if URL is allowed to crawl
     *
     * @param  string $url - url to check
     * @return bool
     */
    public function isAllowed($url)
    {
        return $this->userAgent->check($url, self::DIRECTIVE_ALLOW);
    }

    /**
     * Check if URL is disallowed to crawl
     *
     * @param  string $url - url to check
     * @return bool
     */
    public function isDisallowed($url)
    {
        return $this->userAgent->check($url, self::DIRECTIVE_DISALLOW);
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemap->export();
    }

    /**
     * Get host
     *
     * @return string|null
     */
    public function getHost()
    {
        return $this->host->export();
    }

    /**
     * Get Clean-param
     *
     * @return array
     */
    public function getCleanParam()
    {
        return $this->cleanParam->export();
    }
}
