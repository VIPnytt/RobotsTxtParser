<?php
namespace vipnytt\RobotsTxtParser\Modules;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;

/**
 * Trait DirectiveTools
 *
 * @package vipnytt\RobotsTxtParser\Modules
 */
trait Toolbox
{
    /**
     * Check basic rule
     *
     * @param string $path
     * @param array $paths
     * @return bool
     */
    protected function checkPath($path, $paths)
    {
        foreach ($paths as $rule) {
            $escape = ['?' => '\?', '.' => '\.', '*' => '.*'];
            foreach ($escape as $search => $replace) {
                /**
                 * str_replace needs to be replaced by something better, multi-byte safe
                 */
                $rule = str_replace($search, $replace, $rule);
            }
            /**
             * Warning: preg_match need to be replaced
             *
             * Bug report
             * @link https://github.com/t1gor/Robots.txt-Parser-Class/issues/62
             *
             * An robots.txt parser, where a bug-fix is planned
             * @link https://github.com/diggin/Diggin_RobotRules
             *
             * The solution?
             * PHP PEG (parsing expression grammar)
             * @link https://github.com/hafriedlander/php-peg
             */
            if (preg_match('~' . $rule . '~', $path)) {
                if (mb_stripos($rule, '$') !== false) {
                    if (mb_strlen($rule) - 1 == mb_strlen($path)) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generate directive/rule pair
     *
     * @param string $line
     * @param array $whiteList
     * @return array|false
     */
    protected function generateRulePair($line, $whiteList)
    {
        $whiteList = array_map('mb_strtolower', $whiteList);
        // Split by directive and rule
        $pair = array_map('trim', mb_split(':', $line, 2));
        // Check if the line contains a rule
        if (
            empty($pair[1]) ||
            empty($pair[0]) ||
            !in_array(($pair[0] = mb_strtolower($pair[0])), $whiteList)
        ) {
            // Line does not contain any supported directive
            return false;
        }
        return [
            'directive' => $pair[0],
            'value' => $pair[1],
        ];
    }

    /**
     * Validate directive
     *
     * @param $directive
     * @param $directives
     * @return string
     * @throws ParserException
     */
    protected function validateDirective($directive, $directives)
    {
        if (!in_array($directive, $directives, true)) {
            throw new ParserException('Directive is not allowed here');
        }
        return mb_strtolower($directive);
    }
}
