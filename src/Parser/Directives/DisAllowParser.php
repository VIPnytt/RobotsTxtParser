<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\DisAllowClient;
use vipnytt\RobotsTxtParser\Exceptions;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class DisAllowParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class DisAllowParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;
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
    private $directive;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * Rule array
     * @var array
     */
    private $array = [];

    /**
     * Sub-directive Clean-param
     * @var CleanParamParser
     */
    private $cleanParam;

    /**
     * Sub-directive Host
     * @var HostParser
     */
    private $host;

    /**
     * DisAllow constructor
     *
     * @param string $base
     * @param string $directive
     */
    public function __construct($base, $directive)
    {
        $this->base = $base;
        $this->directive = $this->validateDirective($directive, self::DIRECTIVE);
        $this->cleanParam = new CleanParamParser();
        $this->host = new HostParser($this->base);
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
    private function addPath($rule)
    {
        if (isset($this->array['path']) && in_array($rule, $this->array['path'])) {
            return false;
        }
        $this->array['path'][] = $rule;
        return true;
    }

    /**
     * Rule array
     *
     * @return array
     */
    public function getRules()
    {
        $result = array_merge(
            $this->array,
            $this->cleanParam->getRules(),
            $this->host->getRules()
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

    /**
     * Client
     *
     * @return DisAllowClient
     */
    public function client()
    {
        return new DisAllowClient($this->array, $this->cleanParam->client(), $this->host->client());
    }
}
