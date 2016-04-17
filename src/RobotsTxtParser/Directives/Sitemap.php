<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\UrlToolbox;

/**
 * Class Sitemap
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
final class Sitemap implements DirectiveInterface, RobotsTxtInterface
{
    use UrlToolbox;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_SITEMAP;

    /**
     * Sitemap array
     * @var array
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
     * Export
     *
     * @return array
     */
    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }
}
