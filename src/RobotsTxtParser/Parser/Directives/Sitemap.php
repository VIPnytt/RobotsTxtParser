<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class Sitemap
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class Sitemap implements DirectiveInterface, RobotsTxtInterface
{
    use UrlParser;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_SITEMAP;

    /**
     * Sitemap array
     * @var string[]
     */
    protected $array = [];

    /**
     * Sitemap constructor.
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
        if (
            !$this->urlValidate(($url = $this->urlEncode($line))) ||
            in_array($url, $this->array)
        ) {
            return false;
        }
        $this->array[] = $url;
        return true;
    }

    /**
     * Export rules
     *
     * @return string[][]
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
        foreach ($this->array as $value) {
            $result[] = self::DIRECTIVE . ': ' . $value;
        }
        return $result;
    }
}
