<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions\TxtParserException;

/**
 * Class TxtParser
 *
 * @package vipnytt\RobotsTxtParser
 */
class TxtParser
{
    /**
     * Robots.txt max length in bytes
     */
    const DEFAULT_BYTE_LIMIT = 500000;

    /**
     * Max rule length
     */
    const RULE_MAX_LENGTH = 500;

    /**
     * Directives
     */
    const DIRECTIVE_ALLOW = 'allow';
    const DIRECTIVE_CACHE_DELAY = 'cache-delay'; // unofficial
    const DIRECTIVE_CLEAN_PARAM = 'clean-param'; // Yandex only
    const DIRECTIVE_CRAWL_DELAY = 'crawl-delay';
    const DIRECTIVE_DISALLOW = 'disallow';
    const DIRECTIVE_HOST = 'host';  // Yandex only
    const DIRECTIVE_SITEMAP = 'sitemap';
    const DIRECTIVE_USER_AGENT = 'user-agent';

    /**
     * Default User-Agent
     */
    const FALLBACK_USER_AGENT = '*';

    /**
     * RAW robots.txt content
     * @var string
     */
    private $raw = '';

    /**
     * Rule array
     * @var array
     */
    private $rules = [];

    /**
     * User-Agents
     * @var array
     */
    private $userAgents = [self::FALLBACK_USER_AGENT];

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
     * @param string|null $encoding - character encoding
     * @param int|null $byteLimit - maximum of bytes to parse
     * @throws TxtParserException
     */
    public function __construct($content, $encoding = null, $byteLimit = self::DEFAULT_BYTE_LIMIT)
    {
        if ($encoding === null) {
            $encoding = mb_detect_encoding($content);
        }
        if (!mb_internal_encoding($encoding)) {
            throw new TxtParserException('Unable to set internal character encoding to `' . $encoding . '`');
        }

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
        $lines = array_filter(array_map('trim', mb_split('\n', $this->raw)));
        // Parse each line individually
        foreach ($lines as $this->line) {
            // Limit rule length and remove comments
            $this->line = mb_split('#', mb_substr($this->line, 0, self::RULE_MAX_LENGTH), 2)[0];
            // Parse line
            if (
                ($this->generateRulePair()) === false
                || ($result = $this->parseLine()) === false
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
            empty($pair[1])
            || empty($pair[0])
            || !in_array(($pair[0] = mb_strtolower($pair[0])), $this->directives())
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
            ($this->generateRulePair()) === false
            || !in_array($this->directive, $this->directives($parent))
        ) {
            return false;
        }
        // Cache directive/rule variables to after inline directives has been parsed
        $directive = $this->directive;
        $rule = $this->rule;
        $this->line = $this->rule;
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
     * @return array
     */
    private function addCleanParam()
    {
        $result = [];
        $cleanParam = $this->explodeCleanParamRule($this->rule);
        foreach ($cleanParam['param'] as $param) {
            $result[$this->directive]['path'][$cleanParam['path']]['param'][] = $param;
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
        $cleanParam['path'] = isset($array[1]) ? $this->urlEncode(mb_ereg_replace('[^A-Za-z0-9\.-\/\*\_]', '', $array[1])) : "/*";
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $cleanParam['param'][] = $key;
        }
        return $cleanParam;
    }

    /**
     * URL encoder according to RFC 3986
     * Returns a string containing the encoded URL with disallowed characters converted to their percentage encodings.
     * @link http://publicmind.in/blog/url-encoding/
     *
     * @param string $url
     * @return string
     */
    private function urlEncode($url)
    {
        $reserved = [
            ":" => '!%3A!ui',
            "/" => '!%2F!ui',
            "?" => '!%3F!ui',
            "#" => '!%23!ui',
            "[" => '!%5B!ui',
            "]" => '!%5D!ui',
            "@" => '!%40!ui',
            "!" => '!%21!ui',
            "$" => '!%24!ui',
            "&" => '!%26!ui',
            "'" => '!%27!ui',
            "(" => '!%28!ui',
            ")" => '!%29!ui',
            "*" => '!%2A!ui',
            "+" => '!%2B!ui',
            "," => '!%2C!ui',
            ";" => '!%3B!ui',
            "=" => '!%3D!ui',
            "%" => '!%25!ui'
        ];
        return preg_replace(array_values($reserved), array_keys($reserved), rawurlencode($url));
    }

    /**
     * Add Host
     *
     * @return array|false
     */
    private function addHost()
    {
        if (($parsed = parse_url(($this->rule = $this->urlEncode($this->rule)))) === false) {
            return false;
        }
        $host = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        if (
            !$this->urlValidateHost($host)
            || isset($parsed['scheme']) && !$this->urlValidateScheme($parsed['scheme'])
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
     * Validate host name
     *
     * @link http://stackoverflow.com/questions/1755144/how-to-validate-domain-name-in-php
     *
     * @param  string $host
     * @return bool
     */
    private static function  urlValidateHost($host)
    {
        return (
            mb_ereg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && mb_ereg_match("/^.{1,253}$/", $host) //overall length check
            && mb_ereg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host) //length of each label
            && !filter_var($host, FILTER_VALIDATE_IP) //is not an IP address
        );
    }

    /**
     * Validate URL scheme
     *
     * @param  string $scheme
     * @return bool
     */
    private static function urlValidateScheme($scheme)
    {
        return in_array($scheme, [
                'http', 'https',
                'ftp', 'sftp'
            ]
        );
    }

    /**
     * Add Sitemap
     *
     * @return array|false
     */
    private function addSitemap()
    {
        if (!$this->urlValidate(($url = $this->urlEncode($this->rule)))) {
            return false;
        }
        return [
            self::DIRECTIVE_SITEMAP => [
                $url
            ]
        ];
    }

    /**
     * Validate URL
     *
     * @param string $url
     * @return bool
     */
    public function urlValidate($url)
    {
        return (
            filter_var($url, FILTER_VALIDATE_URL)
            && ($parsed = parse_url($url)) !== false
            && $this->urlValidateHost($parsed['host'])
            && $this->urlValidateScheme($parsed['scheme'])
        );
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
    public function getRules()
    {
        return $this->rules;
    }
}
