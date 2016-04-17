<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;
use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class DisAllow
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class DisAllow implements DirectiveInterface, RobotsTxtInterface
{
    use ObjectTools;

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
     * @throws ParserException
     */
    public function __construct($directive)
    {
        if (!in_array($directive, self::DIRECTIVE, true)) {
            throw new ParserException('Directive not allowed here, has to be `' . self::DIRECTIVE_ALLOW . '` or `' . self::DIRECTIVE_DISALLOW . '`');
        }
        $this->directive = mb_strtolower($directive);
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
        // Return an array of paths
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
        return (
            $this->checkPath($path, isset($this->array['path']) ? $this->array['path'] : []) ||
            $this->cleanParam->check($path) ||
            $this->host->check($url)
        );
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        $result = $this->array
            + $this->cleanParam->export()
            + $this->host->export();
        return empty($result) ? [] : [$this->directive => $result];
    }
}
