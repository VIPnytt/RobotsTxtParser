<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CleanParamParserCore
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
abstract class CleanParamParserCore implements ParserInterface, RobotsTxtInterface
{
    /**
     * Clean-param array
     * @var string[][]
     */
    protected $cleanParam = [];

    /**
     * CleanParamParserCore constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        // split into parameter and path
        $array = array_map('trim', mb_split('\s+', $line, 2));
        // strip any invalid characters from path prefix
        $path = '/';
        if (isset($array[1])) {
            $uriParser = new UriParser(preg_replace('/[^A-Za-z0-9\.-\/\*\_]/', '', $array[1]));
            $path = $uriParser->encode();
        }
        $param = array_map('trim', explode('&', $array[0]));
        foreach ($param as $key) {
            $this->cleanParam[$key][] = $path;
        }
        return true;
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        ksort($this->cleanParam);
        return $handler->getMode() >= 3 ? $this->renderExtensive($handler) : $this->renderCompressed($handler);
    }

    /**
     * Render extensive
     *
     * @param RenderHandler $handler
     * @return bool
     */
    private function renderExtensive(RenderHandler $handler)
    {
        foreach ($this->cleanParam as $param => $paths) {
            foreach ($paths as $path) {
                $handler->add(self::DIRECTIVE_CLEAN_PARAM, $param . ' ' . $path);
            }
        }
        return true;
    }

    /**
     * Render compressed
     *
     * @param RenderHandler $handler
     * @return bool
     */
    private function renderCompressed(RenderHandler $handler)
    {
        $pair = $this->cleanParam;
        while (!empty($pair)) {
            $equalParams = array_keys($pair, current($pair));
            foreach (current($pair) as $path) {
                $handler->add(self::DIRECTIVE_CLEAN_PARAM, implode('&', $equalParams) . ' ' . $path);
            }
            foreach ($equalParams as $param) {
                unset($pair[$param]);
            }
        }
        return true;
    }
}
