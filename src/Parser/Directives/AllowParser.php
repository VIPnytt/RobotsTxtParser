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
     * @var InlineCleanParamParser
     */
    private $cleanParam;

    /**
     * Sub-directive Host
     * @var InlineHostParser
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
        $this->cleanParam = new InlineCleanParamParser();
        $this->host = new InlineHostParser($base, $effective);
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
        foreach ([
                     $path,
                     '/',
                     '*',
                 ] as $testPath) {
            if (in_array($testPath, $this->path)) {
                return false;
            }
        }
        if ($this->isPath($path)) {
            $this->path[] = $path;
            $this->removeOverlapping();
        }
        return in_array($path, $this->path);
    }

    /**
     * Check if path is valid
     *
     * @param string $path
     * @return bool
     */
    private function isPath($path)
    {
        if (mb_strpos($path, '/') !== 0) {
            foreach (
                [
                    '*',
                    '?',
                ] as $char) {
                $path = str_replace($char, '/', $path);
            }
        }
        return mb_strpos($path, '/') === 0;
    }

    /**
     * Remove overlapping paths
     *
     * @return bool
     */
    private function removeOverlapping()
    {
        foreach ($this->path as $key1 => $path1) {
            foreach ($this->path as $key2 => $path2) {
                if (
                    $key1 !== $key2 &&
                    mb_strpos($path1, $path2) === 0
                ) {
                    unset($this->path[$key1]);
                    return $this->removeOverlapping();
                }
            }
        }
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
