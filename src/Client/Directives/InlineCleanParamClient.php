<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class InlineCleanParamClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/InlineCleanParamClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class InlineCleanParamClient implements ClientInterface
{
    use DirectiveClientTrait;

    /**
     * Clean-param
     * @var string[][]
     */
    protected $cleanParam = [];

    /**
     * CleanParamClient constructor.
     *
     * @param string[][] $cleanParam
     */
    public function __construct(array $cleanParam)
    {
        $this->cleanParam = $cleanParam;
    }

    /**
     * Export
     *
     * @return string[][]
     */
    public function export()
    {
        return $this->cleanParam;
    }

    /**
     * Detect dynamic parameters
     *
     * @param  string $uri
     * @return string[]
     */
    public function detect($uri)
    {
        return $this->parse($uri, $this->cleanParam);
    }

    /**
     * Parse uri and return detected parameters
     *
     * @param string $uri
     * @param array $pairs
     * @return string[]
     */
    protected function parse($uri, array $pairs)
    {
        $result = [];
        foreach ($pairs as $param => $paths) {
            if ((
                    strpos($uri, "?$param=") ||
                    strpos($uri, "&$param=")
                ) &&
                $this->checkPaths($uri, $paths)
            ) {
                $result[] = $param;
            }
        }
        sort($result);
        return $result;
    }
}
