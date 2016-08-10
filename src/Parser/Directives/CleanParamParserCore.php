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
        if (isset($array[1])) {
            // strip any invalid characters from path prefix
            $uriParser = new UriParser(preg_replace('/[^A-Za-z0-9\.-\/\*\_]/', '', $array[1]));
            $path = rtrim($uriParser->encode(), '*');
        }
        $path = empty($path) ? '/' : $path;
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
        return $handler->getLevel() >= 3 ? $this->renderExtensive($handler) : $this->renderCompressed($handler);
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
        while (($currentPair = current($pair)) !== false) {
            $equalParams = array_keys($pair, $currentPair);
            foreach ($currentPair as $path) {
                $handler->add(self::DIRECTIVE_CLEAN_PARAM, implode('&', $equalParams) . ' ' . $path);
            }
            foreach ($equalParams as $param) {
                unset($pair[$param]);
            }
        }
        return true;
    }
}
