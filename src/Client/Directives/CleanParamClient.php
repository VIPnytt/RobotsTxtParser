<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class CleanParamClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CleanParamClient
{
    /**
     * Clean-param
     * @var string[]
     */
    private $cleanParam = [];

    /**
     * CleanParamClient constructor.
     *
     * @param string[] $cleanParam
     */
    public function __construct(array $cleanParam)
    {
        $this->cleanParam = $cleanParam;
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        return $this->cleanParam;
    }
}
