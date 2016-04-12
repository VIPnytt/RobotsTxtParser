<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions;

/**
 * Class Read
 *
 * @package vipnytt\RobotsTxtParser
 */
class Parser implements RobotsTxtInterface
{
    use UrlToolbox;

    /**
     * Max length for each rule
     */
    protected $maxRuleLength = self::MAX_LENGTH_RULE;

    /**
     * RAW robots.txt content
     * @var string
     */
    protected $raw = '';

    /**
     * Rule array
     * @var array
     */
    protected $rules = [];

    /**
     * User-Agents
     * @var array
     */
    private $userAgents = [self::USER_AGENT];

    /**
     * Current line
     * @var string
     */
    private $line = '';

    /**
     * Previous directive
     * @var string
     */
    private $previous;

    /**
     * Current Directive
     * @var string
     */
    private $directive;

    /**
     * Current Rule
     * @var array|string
     */
    private $rule;

    /**
     * Constructor
     *
     * @param string $content - file content
     * @param string $encoding - character encoding
     * @param integer|null $byteLimit - maximum of bytes to parse
     * @param integer|null $maxRuleLength - max length of each rule
     * @throws Exceptions\ParserException
     */
    public function __construct($content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT, $maxRuleLength = self::MAX_LENGTH_RULE)
    {
        if (!mb_internal_encoding($encoding)) {
            throw new Exceptions\ParserException('Unable to set internal character encoding to `' . $encoding . '`');
        }
        $this->maxRuleLength = $maxRuleLength;
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
        foreach ($lines as $this->line) {
            // Limit rule length
            if (is_int($this->maxRuleLength)) {
                $this->line = mb_substr($this->line, 0, $this->maxRuleLength);
            }
            // Remove comments
            $this->line = mb_split('#', $this->line, 2)[0];
            // Parse line
            if (
                ($this->generateRulePair()) === false ||
                ($result = $this->parseLine()) === false
            ) {
                continue;
            }
            // Add rule
            $this->previous = $this->directive;
            $this->rule = $result;
            $this->rules = array_merge_recursive($this->assignUserAgent(), $this->rules);
        }
    }

    /**
     * Generate Directive:Rule pair
     *
     * @return bool
     */
    private function generateRulePair()
    {
        // Split by directive and rule
        $pair = array_map('trim', mb_split(':', $this->line, 2));
        // Check if the line contains a rule
        if (
            empty($pair[1]) ||
            empty($pair[0]) ||
            !in_array(($pair[0] = mb_strtolower($pair[0])), $this->directives())
        ) {
            // Line does not contain any supported directive
            return false;
        }
        $this->directive = $pair[0];
        $this->rule = $pair[1];
        return true;
    }

    /**
     * Directives and sub directives
     *
     * @param string|null $parent
     * @return array
     */
    private function directives($parent = null)
    {
        $array = [
            self::DIRECTIVE_ALLOW => [
                self::DIRECTIVE_CLEAN_PARAM,
                self::DIRECTIVE_HOST,
            ],
            self::DIRECTIVE_CACHE_DELAY => [],
            self::DIRECTIVE_CLEAN_PARAM => [],
            self::DIRECTIVE_CRAWL_DELAY => [],
            self::DIRECTIVE_DISALLOW => [
                self::DIRECTIVE_CLEAN_PARAM,
                self::DIRECTIVE_HOST,
            ],
            self::DIRECTIVE_HOST => [],
            self::DIRECTIVE_SITEMAP => [],
            self::DIRECTIVE_USER_AGENT => [
                self::DIRECTIVE_ALLOW,
                self::DIRECTIVE_CACHE_DELAY,
                self::DIRECTIVE_CRAWL_DELAY,
                self::DIRECTIVE_DISALLOW,
            ],
        ];
        if ($parent !== null) {
            return isset($array[$parent]) ? $array[$parent] : [];
        }
        return array_keys($array);
    }

    /**
     * Parse line
     *
     * @param string|null $parent
     * @return array|false
     */
    private function parseLine($parent = null)
    {
        if (
            ($this->generateRulePair()) === false ||
            !in_array($this->directive, $this->directives($parent))
        ) {
            return false;
        }
        // Cache directive/rule variables to after inline directives has been parsed
        $directive = $this->directive;
        $rule = $this->rule;
        $this->line = (string)$this->rule;
        if (($inline = $this->parseLine($this->directive)) !== false) {
            $rule = $inline;
        };
        $this->directive = $directive;
        $this->rule = $rule;
        return $this->add();
    }

    /**
     * Add value to directive
     *
     * @return array|false
     */
    private function add()
    {
        switch ($this->directive) {
            case self::DIRECTIVE_ALLOW:
            case self::DIRECTIVE_DISALLOW:
                return $this->addDisAllow();
            case self::DIRECTIVE_CACHE_DELAY:
            case self::DIRECTIVE_CRAWL_DELAY:
                return $this->addFloat();
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->addCleanParam();
            case self::DIRECTIVE_HOST:
                return $this->addHost();
            case self::DIRECTIVE_SITEMAP:
                return $this->addSitemap();
            case self::DIRECTIVE_USER_AGENT:
                return $this->setUserAgent();
        }
        return false;
    }

    /**
     * Add an Allow or Disallow rule
     *
     * @return array
     */
    private function addDisAllow()
    {
        // If inline directive, pass the array
        if (is_array($this->rule)) {
            return [
                $this->directive => $this->rule
            ];
        }
        // Return an array of paths
        return [
            $this->directive => [
                'path' => [
                    $this->rule
                ]
            ]
        ];
    }

    /**
     * Add float value
     *
     * @return array|false
     */
    private function addFloat()
    {
        if (empty(($float = floatval($this->rule)))) {
            return false;
        }
        return [
            $this->directive => $float,
        ];
    }

    /**
     * Add Clean-Param record
     *
     * @return array|false
     */
    private function addCleanParam()
    {
        if (!is_string($this->rule)) {
            return false;
        }
        $result = [];
        $cleanParam = $this->explodeCleanParamRule($this->rule);
        foreach ($cleanParam['param'] as $param) {
            $result[$this->directive][$param][] = $cleanParam['path'];
        }
        return $result;
    }

    /**
     * Explode Clean-Param rule
     *
     * @param  string $rule
     * @return array
     */
    private function explodeCleanParamRule($rule)
    {
        // split into parameter and path
        $array = array_map('trim', mb_split('\s+', $rule, 2));
        $cleanParam = [];
        // strip any invalid characters from path prefix
        $cleanParam['path'] = isset($array[1]) ? $this->urlEncode(mb_ereg_replace('[^A-Za-z0-9\.-\/\*\_]', '', $array[1])) : '/*';
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $cleanParam['param'][] = $key;
        }
        return $cleanParam;
    }

    /**
     * Add Host
     *
     * @return array|false
     */
    private function addHost()
    {
        if (
            !is_string($this->rule) ||
            ($parsed = parse_url(($this->rule = $this->urlEncode(mb_strtolower($this->rule))))) === false
        ) {
            return false;
        }
        $host = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        if (
            !$this->urlValidateHost($host) ||
            isset($parsed['scheme']) &&
            !$this->urlValidateScheme($parsed['scheme'])
        ) {
            return false;
        }
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        return [
            self::DIRECTIVE_HOST => [
                $scheme . $host . $port,
            ]
        ];
    }

    /**
     * Add Sitemap
     *
     * @return array|false
     */
    private function addSitemap()
    {
        if (
            !is_string($this->rule) ||
            !$this->urlValidate(($url = $this->urlEncode($this->rule)))
        ) {
            return false;
        }
        return [
            self::DIRECTIVE_SITEMAP => [
                $url
            ]
        ];
    }

    /**
     * Set User-Agent(s)
     *
     * @return array
     */
    private function setUserAgent()
    {
        switch ($this->previous) {
            case self::DIRECTIVE_USER_AGENT:
                $this->userAgents[] = $this->rule;
                break;
            default:
                $this->userAgents = [
                    $this->rule
                ];
        }
        return [];
    }

    /**
     * Assign User-Agent dependent rules to the User-Agent arrays
     *
     * @return array
     */
    private function assignUserAgent()
    {
        if (in_array($this->directive, $this->directives(self::DIRECTIVE_USER_AGENT))) {
            $rule = [];
            foreach ($this->userAgents as $userAgent) {
                $rule[self::DIRECTIVE_USER_AGENT][$userAgent] = $this->rule;
            }
            return $rule;
        }
        return $this->rule;
    }

    /**
     * Get rules
     */
    public function export()
    {
        return $this->rules;
    }
}
