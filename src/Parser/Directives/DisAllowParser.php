<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\DisAllowClient;
use vipnytt\RobotsTxtParser\Exceptions;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class DisAllowParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class DisAllowParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

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
     * Path
     * @var array
     */
    private $path = [];

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
        $this->directive = $this->validateDirective($directive, [self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW]);
        $this->cleanParam = new CleanParamParser($this->directive);
        $this->host = new HostParser($this->base, $this->directive);
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
     * @param string $path
     * @return bool
     */
    private function addPath($path)
    {
        if (in_array($path, $this->path)) {
            return false;
        }
        $this->path[] = $path;
        return true;
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
            $this->path,
            $this->cleanParam->render(),
            $this->host->render()
        );
        foreach ($render as $value) {
            $result[] = $this->directive . ':' . $value;
        }
        sort($result);
        return $result;
    }

    /**
     * Client
     *
     * @return DisAllowClient
     */
    public function client()
    {
        return new DisAllowClient($this->path, $this->host->client(), $this->cleanParam->client());
    }
}
