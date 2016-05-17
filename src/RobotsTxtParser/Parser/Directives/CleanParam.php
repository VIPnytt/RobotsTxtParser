<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Parser\Toolbox;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class CleanParam
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CleanParam implements DirectiveInterface, RobotsTxtInterface
{
    use Toolbox;
    use UrlParser;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_CLEAN_PARAM;

    /**
     * Clean-param array
     * @var array
     */
    protected $array = [];

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
     * Check
     *
     * @param  string $path
     * @return bool
     */
    public function check($path)
    {
        foreach ($this->array as $param => $paths) {
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
     * Export rules
     *
     * @return string[][][]
     */
    public function export()
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
