<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Handler\PhpAddOnTrait;

/**
 * Class Import
 *
 * @package vipnytt\RobotsTxtParser
 */
class Import extends TxtClient
{
    use PhpAddOnTrait;

    /**
     * Root level export template
     */
    const TEMPLATE_ROOT = [
        self::DIRECTIVE_HOST => null,
        self::DIRECTIVE_CLEAN_PARAM => [],
        self::DIRECTIVE_SITEMAP => [],
        self::DIRECTIVE_USER_AGENT => []
    ];

    /**
     * User-agent level export template
     */
    const TEMPLATE_SUB = [
        self::DIRECTIVE_ROBOT_VERSION => null,
        self::DIRECTIVE_VISIT_TIME => [],
        self::DIRECTIVE_NO_INDEX => [
            self::DIRECTIVE_HOST => [],
            'path' => [],
            self::DIRECTIVE_CLEAN_PARAM => [],
        ],
        self::DIRECTIVE_DISALLOW =>
            [
                self::DIRECTIVE_HOST => [],
                'path' => [],
                self::DIRECTIVE_CLEAN_PARAM => [],
            ],
        self::DIRECTIVE_ALLOW =>
            [
                self::DIRECTIVE_HOST => [],
                'path' => [],
                self::DIRECTIVE_CLEAN_PARAM => [],
            ],
        self::DIRECTIVE_CRAWL_DELAY => null,
        self::DIRECTIVE_CACHE_DELAY => null,
        self::DIRECTIVE_REQUEST_RATE => [],
        self::DIRECTIVE_COMMENT => [],
    ];

    /**
     * Array
     * @var array
     */
    private $array;

    /**
     * Import constructor.
     *
     * @param array $export
     * @param string $baseUri
     */
    public function __construct(array $export, $baseUri = 'https://example.com')
    {
        $this->array = $this->arrayMergeRecursiveEx(self::TEMPLATE_ROOT, $export);
        foreach (array_keys($this->array[self::DIRECTIVE_USER_AGENT]) as $userAgent) {
            $this->array[self::DIRECTIVE_USER_AGENT][$userAgent] = $this->arrayMergeRecursiveEx(self::TEMPLATE_SUB, $this->array[self::DIRECTIVE_USER_AGENT][$userAgent]);
        }
        parent::__construct($baseUri, null, implode(PHP_EOL, array_merge(
            $this->buildHost($this->array[self::DIRECTIVE_HOST]),
            $this->buildCleanParam($this->array[self::DIRECTIVE_CLEAN_PARAM]),
            $this->buildGenericArray($this->array[self::DIRECTIVE_SITEMAP], self::DIRECTIVE_SITEMAP),
            $this->buildUserAgent($this->array[self::DIRECTIVE_USER_AGENT])
        )));
    }

    /**
     * Host
     *
     * @param string[]|string|null $array
     * @return string[]
     */
    private function buildHost($array)
    {
        if (!is_array($array)) {
            $array = [$array];
        }
        return preg_filter('/^/', self::DIRECTIVE_HOST . ':', $array);
    }

    /**
     * Clean-param
     *
     * @param string[][] $array
     * @return string[]
     */
    private function buildCleanParam($array)
    {
        $result = [];
        foreach ($array as $param => $paths) {
            foreach ($paths as $path) {
                $result[] = self::DIRECTIVE_CLEAN_PARAM . ':' . $param . ' ' . $path;
            }
        }
        return $result;
    }

    /**
     * Comment | Sitemap
     *
     * @param string[] $array
     * @param string $directive
     * @return string[]
     */
    private function buildGenericArray($array, $directive)
    {
        return preg_filter('/^/', $directive . ':', $array);
    }

    /**
     * User-agent
     *
     * @param array $array
     * @return string[]
     */
    private function buildUserAgent($array)
    {
        $result = [];
        foreach ($array as $userAgent => $rules) {
            $result = array_merge(
                $result,
                [self::DIRECTIVE_USER_AGENT . ':' . $userAgent],
                $this->buildGenericString($rules[self::DIRECTIVE_ROBOT_VERSION], self::DIRECTIVE_ROBOT_VERSION),
                $this->buildVisitTime($rules[self::DIRECTIVE_VISIT_TIME]),
                $this->buildAllow($rules[self::DIRECTIVE_NO_INDEX], self::DIRECTIVE_NO_INDEX),
                $this->buildAllow($rules[self::DIRECTIVE_DISALLOW], self::DIRECTIVE_DISALLOW),
                $this->buildAllow($rules[self::DIRECTIVE_ALLOW], self::DIRECTIVE_ALLOW),
                $this->buildGenericString($rules[self::DIRECTIVE_CRAWL_DELAY], self::DIRECTIVE_CRAWL_DELAY),
                $this->buildGenericString($rules[self::DIRECTIVE_CACHE_DELAY], self::DIRECTIVE_CACHE_DELAY),
                $this->buildRequestRate($rules[self::DIRECTIVE_REQUEST_RATE]),
                $this->buildGenericArray($rules[self::DIRECTIVE_COMMENT], self::DIRECTIVE_COMMENT)
            );
        }
        return $result;
    }

    /**
     * Cache-delay | Comment | Crawl-delay | Robot-version
     *
     * @param float|int|string|null $value
     * @param string $directive
     * @return string[]
     */
    private function buildGenericString($value, $directive)
    {
        return [$directive . ':' . $value];
    }

    /**
     * Visit-time
     *
     * @param int[]|string[] $array
     * @return string[]
     */
    private function buildVisitTime($array)
    {
        $result = [];
        foreach ($array as $pair) {
            $result[] = self::DIRECTIVE_VISIT_TIME . ':' . $pair['from'] . '-' . $pair['to'];
        }
        return $result;
    }

    /**
     * Allow / Disallow / NoIndex
     *
     * @param array $array
     * @param string $directive
     * @return string[]
     */
    private function buildAllow($array, $directive)
    {
        return preg_filter('/^/', $directive . ':', array_merge(
                $this->buildHost($array[self::DIRECTIVE_HOST]),
                $this->buildCleanParam($array[self::DIRECTIVE_CLEAN_PARAM]),
                $array['path']
            )
        );
    }

    /**
     * Request-rate
     *
     * @param array $array
     * @return string[]
     */
    private function buildRequestRate($array)
    {
        $result = [];
        foreach ($array as $pair) {
            $string = self::DIRECTIVE_REQUEST_RATE . ':1/' . $pair['rate'] . 's';
            if (isset($pair['from']) &&
                isset($pair['to'])
            ) {
                $string .= ' ' . $pair['from'] . '-' . $pair['to'];
            }
            $result[] = $string;
        }
        return $result;
    }

    /**
     * Get difference
     *
     * @return array
     */
    public function getIgnoredImportData()
    {
        $source = $this->array;
        $source = $this->arrayFilterRecursive($source);
        array_multisort($source);
        $parsed = $this->arrayFilterRecursive($this->export());
        array_multisort($parsed);
        return $this->arrayDiffAssocRecursive($source, $parsed);
    }
}
