<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\UrlToolbox;

/**
 * Class CleanParam
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class CleanParam implements DirectiveInterface
{
    use ObjectTools;
    use UrlToolbox;

    /**
     * Directive
     */
    const DIRECTIVE = 'Clean-param';

    protected $array = [];
    protected $parent;

    public function __construct($array = [], $parent = null)
    {
        $this->array = $array;
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
        $path = isset($array[1]) ? $this->urlEncode(mb_ereg_replace('[^A-Za-z0-9\.-\/\*\_]', '', $array[1])) : '/*';
        $param = array_map('trim', mb_split('&', $array[0]));
        foreach ($param as $key) {
            $this->array[$key][] = $path;
        }
        return true;
    }

    /**
     * Check Clean-Param rule
     *
     * @param  string $path
     * @return bool
     */
    public function check($path)
    {
        foreach ($this->array as $param => $paths) {
            if (
                mb_strpos($path, "?$param=") ||
                mb_strpos($path, "&$param=")
            ) {
                if (empty($paths)) {
                    return true;
                }
                if ($this->checkPath($path, $paths)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Rule export
     *
     * @return array
     */
    public function getArray()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }

    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }
}
