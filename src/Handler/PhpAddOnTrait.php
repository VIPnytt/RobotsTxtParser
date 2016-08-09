<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

/**
 * Class PhpAddOnTrait
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
trait PhpAddOnTrait
{
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

    /**
     * array_filter_recursive
     * @link http://php.net/manual/en/function.array-filter.php#87581
     *
     * @param array $array
     * @return array
     */
    private function arrayFilterRecursive(array $array)
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
}
