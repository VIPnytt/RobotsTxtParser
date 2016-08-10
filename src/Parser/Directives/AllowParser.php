<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\AllowClient;
use vipnytt\RobotsTxtParser\Exceptions;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class AllowParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class AllowParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserTrait;

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
     * Optimized for performance
     * @var bool
     */
    private $optimized = false;

    /**
     * Client cache
     * @var AllowClient
     */
    private $client;

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
        $path = rtrim($path, '*');
        if (!in_array('/', $this->path) &&
            in_array(mb_substr($path, 0, 1), [
                '/',
                '*',
            ])
        ) {
            $this->path[] = $path;
            $this->optimized = false;
        }
        return in_array($path, $this->path);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        if (!$this->optimized) {
            $this->removeOverlapping();
        }
        sort($this->path);
        $inline = new RenderHandler($handler->getLevel());
        $this->host->render($inline);
        $this->cleanParam->render($inline);
        $handler->addInline($this->directive, $inline);
        foreach ($this->path as $path) {
            $handler->add($this->directive, $path);
        }
        return true;
    }

    /**
     * Remove overlapping paths
     *
     * @return bool
     */
    private function removeOverlapping()
    {
        foreach ($this->path as $key1 => &$path1) {
            foreach ($this->path as $key2 => &$path2) {
                if ($key1 !== $key2 &&
                    mb_strpos($path1, $path2) === 0
                ) {
                    unset($this->path[$key1]);
                }
            }
        }
        $this->optimized = true;
        return true;
    }

    /**
     * Client
     *
     * @return AllowClient
     */
    public function client()
    {
        if (isset($this->client)) {
            return $this->client;
        } elseif (!$this->optimized) {
            $this->removeOverlapping();
        }
        return $this->client = new AllowClient($this->path, $this->host->client(), $this->cleanParam->client());
    }
}
