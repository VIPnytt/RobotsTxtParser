<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CleanParamParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CleanParamParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;
    use UrlParser;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_CLEAN_PARAM;

    /**
     * Clean-param array
     * @var array
     */
    private $array = [];

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
        $path = isset($array[1]) ? $this->urlEncode(preg_replace('/[^A-Za-z0-9\.-\/\*\_]/', '', $array[1])) : "/";
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $this->array[$key][] = $path;
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
        return new CleanParamClient($this->array);
    }

    /**
     * Rule array
     *
     * @return string[][][]
     */
    public function getRules()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->array as $param => $paths) {
            foreach ($paths as $path) {
                $result[] = self::DIRECTIVE . ':' . $param . ' ' . $path;
            }
        }
        return $result;
    }
}
