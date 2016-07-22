<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class CleanParamClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/CleanParamClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CleanParamClient extends InlineCleanParamClient
{
    /**
     * Common dynamic uri parameters
     * @var string[]
     */
    protected $commonParam = [
        'popup',
        'ref',
        'token',
        'utm_medium',
        'utm_source',
    ];

    /**
     * CleanParamClient constructor.
     *
     * @param string[][] $cleanParam
     */
    public function __construct(array $cleanParam)
    {
        parent::__construct($cleanParam);
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
}
