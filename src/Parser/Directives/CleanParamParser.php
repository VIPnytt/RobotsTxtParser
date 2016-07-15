<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CleanParamParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CleanParamParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Clean-param array
     * @var string[][]
     */
    private $cleanParam = [];

    /**
     * CleanParam constructor.
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
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $this->cleanParam[$key][] = $path;
        }
        return true;
    }

    /**
     * Client
     *
     * @return CleanParamClient
     */
    public function client()
    {
        return new CleanParamClient($this->cleanParam);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->cleanParam as $param => $paths) {
            foreach ($paths as $path) {
                $result[] = self::DIRECTIVE_CLEAN_PARAM . ':' . $param . ' ' . $path;
            }
        }
        sort($result);
        return $result;
    }
}
