<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Exceptions\TxtParserException;

/**
 * Class Parser
 *
 * @package vipnytt\RobotsTxtParser
 */
class TxtParser
{
    /**
     * Default User-Agent
     */
    const USERAGENT_DEFAULT = '*';

    /**
     * Max rule length
     */
    const RULE_MAX_LENGTH = 500;

    /**
     * Directives
     */
    const DIRECTIVE_ALLOW = 'allow';
    const DIRECTIVE_CACHE_DELAY = 'cache-delay';
    const DIRECTIVE_CLEAN_PARAM = 'clean-param';
    const DIRECTIVE_CRAWL_DELAY = 'crawl-delay';
    const DIRECTIVE_DISALLOW = 'disallow';
    const DIRECTIVE_HOST = 'host';
    const DIRECTIVE_SITEMAP = 'sitemap';
    const DIRECTIVE_USERAGENT = 'user-agent';

    /**
     * User-Agent dependent directives
     */
    const USERAGENT_DEPENDENT_DIRECTIVES = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
        self::DIRECTIVE_DISALLOW,
    ];

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
    private $userAgents = [self::USERAGENT_DEFAULT];

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
     * @var string
     */
    private $rule;

    private $line = '';

    /**
     * Constructor
     *
     * @param string $content - file content
     * @throws TxtParserException
     */
    public function __construct($content)
    {
        mb_language("uni");
        if (!mb_internal_encoding('UTF-8')) {
            throw new TxtParserException('Unable to set internal character encoding to `UTF-8`');
        }
        mb_internal_encoding(mb_detect_encoding($content));
        mb_regex_encoding(mb_detect_encoding($content));
        $this->raw = $content;

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
            // Limit rule length
            $this->line = mb_substr($this->line, 0, self::RULE_MAX_LENGTH);
            // Remove comments
            $this->line = mb_split('#', $this->line, 2)[0];
            // Generate pair
            if (($this->generateRulePair()) === false) {
                continue;
            }
            // Parse line
            if (($result = $this->parseLine()) !== false) {
                $this->previous = $this->directive;
                $this->rule = $result;
                $this->rules = array_merge_recursive($this->assignUserAgent(), $this->rules);
            }
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
        // Validate rule
        if (empty($pair[1])) {
            // Line does not contain any rule
            return false;
        }
        // Validate directive
        $pair[0] = mb_strtolower($pair[0]);
        if (empty($pair[0]) || !in_array($pair[0], $this->directives())) {
            // Line does not contain any supported directive
            return false;
        }
        $this->directive = $pair[0];
        $this->rule = $pair[1];
        $this->line = $this->rule;
        return true;
    }

    /**
     * Directives
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
            self::DIRECTIVE_USERAGENT => [],
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
        if (($this->generateRulePair()) === false) {
            return false;
        }
        if (!in_array($this->directive, $this->directives($parent))) {
            return false;
        }
        // Cache directive/rule variables to after inline directives has been parsed
        $directive = $this->directive;
        $rule = $this->rule;
        $this->line = $this->rule;
        if (($inline = $this->parseLine($this->directive)) !== false) {
            $pair[1] = $inline;
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
            case self::DIRECTIVE_USERAGENT:
                return $this->setUserAgent();
            case self::DIRECTIVE_CACHE_DELAY:
            case self::DIRECTIVE_CRAWL_DELAY:
                return $this->addFloat();
            case self::DIRECTIVE_HOST:
                return $this->addHost();
            case self::DIRECTIVE_SITEMAP:
                return $this->addSitemap();
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->addCleanParam();
            case self::DIRECTIVE_ALLOW:
            case self::DIRECTIVE_DISALLOW:
                return $this->addDisAllow();
        }
        return false;
    }

    /**
     * Set User-Agent(s)
     *
     * @return array
     */
    private function setUserAgent()
    {
        switch ($this->previous) {
            case self::DIRECTIVE_USERAGENT:
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
     * Add Host
     *
     * @return array|false
     */
    private function addHost()
    {
        $parsed = parse_url($this->UrlEncode($this->rule));
        if (isset($this->host) || $parsed === false) {
            return false;
        }
        $host = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        if (!$this->UrlValidateHost($host)) {
            return false;
        } elseif (isset($parsed['scheme']) && !$this->UrlValidateScheme($parsed['scheme'])) {
            return false;
        }
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        if ($this->rule !== $scheme . $host . $port) {
            return false;
        }
        return [
            self::DIRECTIVE_HOST => [
                $this->rule,
            ]
        ];
    }

    /**
     * URL encoder according to RFC 3986
     * Returns a string containing the encoded URL with disallowed characters converted to their percentage encodings.
     * @link http://publicmind.in/blog/url-encoding/
     *
     * @param string $url
     * @return string
     */
    private function UrlEncode($url)
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
     * Validate host name
     *
     * @link http://stackoverflow.com/questions/1755144/how-to-validate-domain-name-in-php
     *
     * @param  string $host
     * @return bool
     */
    private static function  UrlValidateHost($host)
    {
        return (
            preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && preg_match("/^.{1,253}$/", $host) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host) //length of each label
            && !filter_var($host, FILTER_VALIDATE_IP) //is not an IP address
        );
    }

    /**
     * Validate URL scheme
     *
     * @param  string $scheme
     * @return bool
     */
    private static function UrlValidateScheme($scheme)
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
        if (!$this->UrlValidate(($url = $this->UrlEncode($this->rule)))) {
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
    public function UrlValidate($url)
    {
        return (
            filter_var($url, FILTER_VALIDATE_URL)
            && ($parsed = parse_url($url)) !== false
            && $this->UrlValidateHost($parsed['host'])
            && $this->UrlValidateScheme($parsed['scheme'])
        );
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
        // strip multi-spaces
        $rule = mb_ereg_replace('/\s+/S', ' ', $rule);
        // split into parameter and path
        $array = mb_split(' ', $rule, 2);
        $cleanParam = [];
        // strip any invalid characters from path prefix

        $cleanParam['path'] = isset($array[1]) ? $this->UrlEncode(mb_ereg_replace('/[^A-Za-z0-9\.-\/\*\_]/', '', $array[1])) : "/*";
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $cleanParam['param'][] = $key;
        }
        return $cleanParam;
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
     * Assign User-Agent dependent rules to the User-Agent arrays
     *
     * @return array
     */
    private function assignUserAgent()
    {
        if (in_array($this->directive, self::USERAGENT_DEPENDENT_DIRECTIVES)) {
            $rule = [];
            foreach ($this->userAgents as $userAgent) {
                $rule[self::DIRECTIVE_USERAGENT][$userAgent] = $this->rule;
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
