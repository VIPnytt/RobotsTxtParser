<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\AllowClient;
use vipnytt\RobotsTxtParser\Exceptions;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class AllowParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class AllowParser implements ParserInterface, RobotsTxtInterface
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
     * AllowParser constructor
     *
     * @param string $base
     * @param string $effective
     * @param string $directive
     */
    public function __construct($base, $effective, $directive)
    {
        $this->directive = $directive;
        $this->cleanParam = new CleanParamParser();
        $this->host = new HostParser($base, $effective, $this->directive);
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
        foreach (
            [
                $this->host->render(),
                $this->path,
                $this->cleanParam->render(),
            ] as $values
        ) {
            sort($values);
            foreach ($values as $value) {
                $result[] = $this->directive . ':' . $value;
            }
        }
        return $result;
    }

    /**
     * Client
     *
     * @return AllowClient
     */
    public function client()
    {
        return new AllowClient($this->path, $this->host->client(), $this->cleanParam->client());
    }
}
