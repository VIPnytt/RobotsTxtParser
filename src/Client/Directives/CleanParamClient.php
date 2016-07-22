<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class CleanParamClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/CleanParamClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CleanParamClient implements ClientInterface
{
    use DirectiveClientCommons;

    /**
     * Common dynamic uri parameters
     * @var string
     */
    protected $commonParam = [
        'popup',
        'ref',
        'token'
    ];

    /**
     * Clean-param
     * @var string[][]
     */
    private $cleanParam = [];

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
     * Has robots.txt defined dynamic or common dynamic parameters check
     *
     * @param string $uri
     * @param string[] $customParam
     * @return string[]
     */
    public function detectWithCommon($uri, array $customParam = [])
    {
        $pairs = array_merge_recursive(
            $this->cleanParam,
            $this->appendPath($this->commonParam),
            $this->appendPath($customParam)
        );
        return $this->parse($uri, $pairs);
    }

    /**
     * Convert param list to an valid Clean-param list
     *
     * @param string[] $parameters
     * @return array
     */
    private function appendPath(array $parameters)
    {
        $result = [];
        foreach ($parameters as $parameter) {
            $result[$parameter] = ['/'];
        }
        return $result;
    }

    /**
     * Parse uri and return detected parameters
     *
     * @param string $uri
     * @param array $pairs
     * @return string[]
     */
    private function parse($uri, array $pairs)
    {
        $result = [];
        foreach ($pairs as $param => $paths) {
            if (
                (
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
     * Export
     *
     * @return string[][]
     */
    public function export()
    {
        return $this->cleanParam;
    }
}
