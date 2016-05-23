<?php
namespace vipnytt\RobotsTxtParser\Core\Directives;

use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Core\Toolbox;
use vipnytt\RobotsTxtParser\Core\UrlParser;
use vipnytt\RobotsTxtParser\Exceptions;

/**
 * Class DisAllow
 *
 * @package vipnytt\RobotsTxtParser\Core\Directives
 */
class DisAllow implements DirectiveInterface, RobotsTxtInterface
{
    use Toolbox;
    use UrlParser;

    /**
     * Directive alternatives
     */
    const DIRECTIVE = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_DISALLOW,
    ];

    /**
     * Sub directives white list
     */
    const SUB_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM,
        self::DIRECTIVE_HOST,
    ];

    /**
     * Directive
     * @var string
     */
    protected $directive;

    /**
     * Rule array
     * @var array
     */
    protected $array = [];

    /**
     * Sub-directive Clean-param
     * @var CleanParam
     */
    protected $cleanParam;

    /**
     * Sub-directive Host
     * @var Host
     */
    protected $host;

    /**
     * DisAllow constructor
     *
     * @param string $directive
     */
    public function __construct($directive)
    {
        $this->directive = $this->validateDirective($directive, self::DIRECTIVE);
        $this->cleanParam = new CleanParam();
        $this->host = new Host();
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        switch ($pair['directive']) {
            case self::DIRECTIVE_CLEAN_PARAM:
                return $this->cleanParam->add($pair['value']);
            case self::DIRECTIVE_HOST:
                return $this->host->add($pair['value']);
        }
        return $this->addPath($line);
    }

    /**
     * Add plain path to allow/disallow
     *
     * @param string $rule
     * @return bool
     */
    protected function addPath($rule)
    {
        if (isset($this->array['path']) && in_array($rule, $this->array['path'])) {
            return false;
        }
        $this->array['path'][] = $rule;
        return true;
    }

    /**
     * Check
     *
     * @param  string $url
     * @return bool
     */
    public function check($url)
    {
        $path = $this->getPath($url);
        return ($path === false) ? false : (
            $this->checkPath($path, isset($this->array['path']) ? $this->array['path'] : []) ||
            $this->cleanParam->check($path) ||
            $this->host->check($url)
        );
    }

    /**
     * Get path and query
     *
     * @param string $url
     * @return string
     * @throws Exceptions\ClientException
     */
    protected function getPath($url)
    {
        // Encode
        $url = $this->urlEncode($url);
        if (mb_stripos($url, '/') === 0) {
            // Strip fragments
            $url = mb_split('#', $url)[0];
            return $url;
        }
        if (!$this->urlValidate($url)) {
            throw new Exceptions\ClientException('Invalid URL');
        }
        $path = (($path = parse_url($url, PHP_URL_PATH)) === null) ? '/' : $path;
        $query = (($query = parse_url($url, PHP_URL_QUERY)) === null) ? '' : '?' . $query;
        return $path . $query;
    }

    /**
     * Export rules
     *
     * @return array
     */
    public function export()
    {
        $result = array_merge(
            $this->array,
            $this->cleanParam->export(),
            $this->host->export()
        );
        return empty($result) ? [] : [$this->directive => $result];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        $render = array_merge(
            $this->array,
            $this->cleanParam->render(),
            $this->host->render()
        );
        foreach ($render as $value) {
            if (is_array($value)) {
                foreach ($value as $path) {
                    $result[] = $this->directive . ':' . $path;
                }
                continue;
            }
            $result[] = $this->directive . ':' . $value;
        }
        return $result;
    }
}
