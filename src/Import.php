<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

/**
 * Class Import
 *
 * @package vipnytt\RobotsTxtParser
 */
class Import extends TxtClient
{
    /**
     * Root level export template
     */
    const TEMPLATE_ROOT = [
        self::DIRECTIVE_CLEAN_PARAM => [],
        self::DIRECTIVE_HOST => null,
        self::DIRECTIVE_SITEMAP => [],
        self::DIRECTIVE_USER_AGENT => []
    ];

    /**
     * User-agent level export template
     */
    const TEMPLATE_SUB = [
        self::DIRECTIVE_ALLOW => [],
        self::DIRECTIVE_CACHE_DELAY => null,
        self::DIRECTIVE_COMMENT => [],
        self::DIRECTIVE_CRAWL_DELAY => null,
        self::DIRECTIVE_DISALLOW => [],
        self::DIRECTIVE_NO_INDEX => [],
        self::DIRECTIVE_REQUEST_RATE => [],
        self::DIRECTIVE_ROBOT_VERSION => null,
        self::DIRECTIVE_VISIT_TIME => [],
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
        parent::__construct($baseUri, null, implode(PHP_EOL, array_merge(
            $this->buildHost($this->array[self::DIRECTIVE_HOST]),
            $this->buildCleanParam($this->array[self::DIRECTIVE_CLEAN_PARAM]),
            $this->buildArray($this->array[self::DIRECTIVE_SITEMAP], self::DIRECTIVE_SITEMAP),
            $this->buildUserAgent($this->array[self::DIRECTIVE_USER_AGENT])
        )));
    }

    /**
     * array_merge_recursive_ex
     * @link http://stackoverflow.com/a/25712428/4396537
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function arrayMergeRecursiveEx(array $array1, array &$array2)
    {
        foreach ($array2 as $key => &$value) {
            if (is_array($value) &&
                isset($array1[$key]) &&
                is_array($array1[$key])
            ) {
                $array1[$key] = $this->arrayMergeRecursiveEx($array1[$key], $value);
            } elseif (!is_int($key)) {
                $array1[$key] = $value;
            } elseif (!in_array($value, $array1)) {
                $array1[] = $value;
            }
        }
        return $array1;
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
    private function buildArray($array, $directive)
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
            $rules = $this->arrayMergeRecursiveEx(self::TEMPLATE_SUB, $rules);
            $result = array_merge(
                $result,
                [self::DIRECTIVE_USER_AGENT . ':' . $userAgent],
                $this->buildString($rules[self::DIRECTIVE_ROBOT_VERSION], self::DIRECTIVE_ROBOT_VERSION),
                $this->buildVisitTime($rules[self::DIRECTIVE_VISIT_TIME]),
                $this->buildArray($rules[self::DIRECTIVE_NO_INDEX], self::DIRECTIVE_NO_INDEX),
                $this->buildArray($rules[self::DIRECTIVE_DISALLOW], self::DIRECTIVE_DISALLOW),
                $this->buildArray($rules[self::DIRECTIVE_ALLOW], self::DIRECTIVE_ALLOW),
                $this->buildString($rules[self::DIRECTIVE_CRAWL_DELAY], self::DIRECTIVE_CRAWL_DELAY),
                $this->buildString($rules[self::DIRECTIVE_CACHE_DELAY], self::DIRECTIVE_CACHE_DELAY),
                $this->buildRequestRate($rules[self::DIRECTIVE_REQUEST_RATE]),
                $this->buildArray($rules[self::DIRECTIVE_COMMENT], self::DIRECTIVE_COMMENT)
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
    private function buildString($value, $directive)
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
        $pair = [
            'source' => $this->array,
            'parsed' => $this->export(),
        ];
        foreach ($pair as &$array) {
            $array = $this->arrayFilterRecursive($array);
            array_multisort($array);
            $this->kSortRecursive($array);
        }
        return $this->arrayDiffAssocRecursive($pair['source'], $pair['parsed']);
    }

    /**
     * array_filter_recursive
     * @link http://php.net/manual/en/function.array-filter.php#87581
     *
     * @param array $array
     * @return array
     */
    private function arrayFilterRecursive(array &$array)
    {
        foreach ($array as $key => &$item) {
            is_array($item) && $array[$key] = $this->arrayFilterRecursive($item);
            if ($array[$key] === null ||
                $array[$key] === []
            ) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * ksort_recursive
     * @link http://stackoverflow.com/a/2543447/4396537
     *
     * @param array $array
     * @return bool
     */
    private function kSortRecursive(array &$array)
    {
        foreach ($array as &$current) {
            if (is_array($current) &&
                $this->kSortRecursive($current) === false
            ) {
                return false;
            }
        }
        return ksort($array);
    }

    /**
     * array_diff_assoc_recursive
     * @link http://php.net/manual/en/function.array-diff-assoc.php#111675
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function arrayDiffAssocRecursive(array &$array1, array &$array2)
    {
        $difference = [];
        foreach ($array1 as $key => &$value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!empty($newDiff = $this->arrayDiffAssocRecursive($value, $array2[$key]))) {
                    $difference[$key] = $newDiff;
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
}
