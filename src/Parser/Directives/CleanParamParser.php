<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CleanParamParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CleanParamParser implements ParserInterface, RobotsTxtInterface
{
    /**
     * Clean-param array
     * @var string[][]
     */
    private $cleanParam = [];

    /**
     * Sort result
     * @var bool
     */
    private $sort = false;

    /**
     * CleanParamParser constructor.
     */
    public function __construct()
    {
    }

    /**
     * Client
     *
     * @return CleanParamClient
     */
    public function client()
    {
        $this->sort();
        return new CleanParamClient($this->cleanParam);
    }

    /**
     * Sort by length
     *
     * @return bool
     */
    private function sort()
    {
        if ($this->sort) {
            return $this->sort;
        };
        return $this->sort = array_multisort($this->cleanParam) && ksort($this->cleanParam);
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $path = '/';
        // split into parameter and path
        $array = array_map('trim', mb_split('\s+', $line, 2));
        if (isset($array[1])) {
            // strip any invalid characters from path prefix
            $uriParser = new UriParser(preg_replace('/[^A-Za-z0-9\.-\/\*\_]/', '', $array[1]));
            $path = rtrim($uriParser->encode(), '*');
            // make sure path is valid
            $path = in_array(substr($path, 0, 1), [
                '/',
                '*',
            ]) ? $path : '/';
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
        $this->sort();
        return $handler->getLevel() == 2 ? $this->renderExtensive($handler) : $this->renderCompressed($handler);
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
