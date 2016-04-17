<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\UrlToolbox;

/**
 * Class Allow
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class Allow implements DirectiveInterface, RobotsTxtInterface
{
    use UrlToolbox;
    use ObjectTools;

    const SUB_DIRECTIVES = [
        self::DIRECTIVE_CLEAN_PARAM,
        self::DIRECTIVE_HOST,
    ];

    /**
     * Directive
     */
    const DIRECTIVE = 'Allow';

    protected $array = [];
    protected $parent;

    protected $cleanParam;
    protected $host;


    public function __construct($parent = null)
    {
        $this->cleanParam = new CleanParam(self::DIRECTIVE);
        $this->host = new Host(self::DIRECTIVE);
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

    public function export()
    {
        $result = $this->array
            + $this->cleanParam->export()
            + $this->host->export();
        return empty($result) ? [] : [self::DIRECTIVE => $result];
    }
}
