<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;

/**
 * Class CleanParamClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class CleanParamClient
{
    use DirectiveParserCommons;

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
     * Check
     *
     * @param  string $path
     * @return bool
     */
    public function check($path)
    {
        foreach ($this->cleanParam as $param => $paths) {
            if (
                (
                    mb_stripos($path, "?$param=") ||
                    mb_stripos($path, "&$param=")
                ) &&
                $this->checkPath($path, $paths)
            ) {
                return true;
            }
        }
        return false;
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
