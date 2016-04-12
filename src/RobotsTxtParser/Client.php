<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\UserAgentParser;

class Client implements RobotsTxtInterface
{
    use UrlToolbox;

    protected $rules = [];

    protected $url = '';
    protected $statusCode = 200;

    protected $userAgent = self::USER_AGENT;

    /**
     * Constructor
     *
     * @param string $content - file content
     * @param string|null $encoding - character encoding
     * @param integer|null $byteLimit - maximum of bytes to parse
     * @param integer|null $maxRuleLength - max length of each rule
     */
    public function __construct($content, $encoding = null, $byteLimit = self::BYTE_LIMIT, $maxRuleLength = self::MAX_LENGTH_RULE)
    {
        if ($encoding === null) {
            $encoding = mb_detect_encoding($content);
        }
        $read = new Parser($content, $encoding, $byteLimit, $maxRuleLength);
        $this->rules = $read->export();
    }

    public function setOrigin($url, $statusCode)
    {
        if (!($this->urlValidate(($url = $this->urlEncode($url))))) {
            throw new Exceptions\ClientException('Invalid URL');
        }
        $this->url = $url;
        $this->statusCode = $statusCode;
    }

    /**
     * Set UserAgent
     *
     * @param string|null $userAgent
     * @return void
     */
    public function setUserAgent($userAgent)
    {
        if (
            empty($userAgent) ||
            !isset($this->rules[self::DIRECTIVE_USER_AGENT])
        ) {
            $this->userAgent = self::USER_AGENT;
            return;
        }
        $parser = new UserAgentParser($userAgent);
        $this->userAgent = $parser->match(array_keys($this->rules[self::DIRECTIVE_USER_AGENT]));
    }

    /**
     *
     *
     * @param  string $url - url to check
     * @return bool
     */
    public function isAllowed($url)
    {
        return $this->checkRules(self::DIRECTIVE_ALLOW, $this->getPath($url));
    }

    /**
     * Check rules
     *
     * @param  string $type - rule to check
     * @param  string $path - path to check
     * @return bool
     */
    protected function checkRules($type, $path)
    {
        // Check each directive for rules, allowed by default
        $result = ($type === self::DIRECTIVE_ALLOW);
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $directive) {
            if (!isset($this->rules[self::DIRECTIVE_USER_AGENT][$this->userAgent][$directive])) {
                continue;
            }
            foreach ($this->rules[self::DIRECTIVE_USER_AGENT][$this->userAgent][$directive] as $ruleType => $array) {
                // check rule
                if ($this->checkRuleSwitch($ruleType, $path, $array)) {
                    $result = ($type === $directive);
                }
            }
        }
        return $result;
    }

    /**
     * Check rule switch
     *
     * @param  string $type - directive or part of an url
     * @param  string $path
     * @param  array $array
     * @return bool
     */
    private function checkRuleSwitch($type, $path, $array)
    {
        switch ($type) {
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->checkCleanParamRule($path, $array);
            case self::DIRECTIVE_HOST;
                return $this->checkHostRule($array);
        }
        return $this->checkRulePaths($path, $array);
    }

    /**
     * Check Clean-Param rule
     *
     * @param  string $path
     * @param  array $array
     * @return bool
     */
    protected function checkCleanParamRule($path, $array)
    {
        foreach ($array as $param => $paths) {
            if (
                mb_strpos($path, "?$param=") ||
                mb_strpos($path, "&$param=")
            ) {
                if (
                    empty($paths) ||
                    $this->checkRulePaths($path, $paths)
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check basic rule
     *
     * @param  string $path
     * @param  array $array
     * @return bool
     */
    protected function checkRulePaths($path, $array)
    {
        foreach ($array as $robotPath) {
            if (preg_match('#' . $robotPath . '#', $path)) {
                if (mb_strpos($robotPath, '$') !== false) {
                    if (mb_strlen($robotPath) - 1 == mb_strlen($path)) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check Host rule
     *
     * @param  array $array
     * @return bool
     */
    protected function checkHostRule($array)
    {
        if (!isset($this->url)) {
            return false;
        }
        $host = mb_strtolower($this->urlEncode($array[0]));
        $url = [
            'scheme' => parse_url($this->url, PHP_URL_SCHEME),
            'host' => parse_url($this->url, PHP_URL_HOST),
        ];
        $url['port'] = is_int($port = parse_url($this->url, PHP_URL_PORT)) ? $port : getservbyname($url['scheme'], 'tcp');
        $cases = [
            $url['host'],
            $url['host'] . ':' . $url['port'],
            $url['scheme'] . '://' . $url['host'],
            $url['scheme'] . '://' . $url['host'] . ':' . $url['port']
        ];
        if (in_array($host, $cases)) {
            return true;
        }
        return false;
    }

    /**
     * Get path
     *
     * @param string $url
     * @return string
     * @throws Exceptions\ClientException
     */
    protected function getPath($url)
    {
        $url = $this->urlEncode($url);
        if (mb_stripos($url, '/') === 0) {
            // URL already is a path
            return $url;
        }
        if (!$this->urlValidate($url)) {
            throw new Exceptions\ClientException('Invalid URL');
        }
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     *
     *
     * @param  string $url - url to check
     * @return bool
     */
    public function isDisallowed($url)
    {
        return $this->checkRules(self::DIRECTIVE_DISALLOW, $this->getPath($url));
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    public function getSitemaps()
    {
        if (empty($sitemaps = $this->rules[self::DIRECTIVE_SITEMAP])) {
            return [];
        }
        return $sitemaps;
    }

    /**
     * Get host
     *
     * @return string|null
     */
    public function getHost()
    {
        if (empty($host = $this->rules[self::DIRECTIVE_HOST][0])) {
            return null;
        }
        return $host;
    }

    /**
     * Get Clean-param
     *
     * @return array
     */
    public function getCleanParam()
    {
        if (empty($cleanParam = $this->rules[self::DIRECTIVE_CLEAN_PARAM])) {
            return [];
        }
        return $cleanParam;
    }

    /**
     * Get CacheDelay
     *
     * @param bool $fallback return Crawl-delay if not found
     * @return int|float|null
     */
    public function getCacheDelay($fallback = true)
    {
        if (empty($cacheDelay = $this->rules[self::DIRECTIVE_CACHE_DELAY])) {
            return ($fallback) ? $this->getCrawlDelay() : null;
        }
        return $cacheDelay;
    }

    /**
     * Get CrawlDelay
     *
     * @return int|float
     */
    public function getCrawlDelay()
    {
        if (empty($crawlDelay = $this->rules[self::DIRECTIVE_CRAWL_DELAY])) {
            return 0;
        }
        return $crawlDelay;
    }
}
