<?php
namespace vipnytt\RobotsTxtParser\Modules\Directives;

use vipnytt\RobotsTxtParser\Modules\UrlTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class Sitemap
 *
 * @package vipnytt\RobotsTxtParser\Modules\Directives
 */
class Sitemap implements DirectiveInterface, RobotsTxtInterface
{
    use UrlTools;

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
