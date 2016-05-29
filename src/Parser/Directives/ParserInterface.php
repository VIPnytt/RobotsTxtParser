<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

/**
 * Interface DirectiveInterface
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
interface ParserInterface
{
    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line);

    /**
     * Export rules
     *
     * @return array
     */
    public function export();

    /**
     * Render
     *
     * @return string[]
     */
    public function render();
}
