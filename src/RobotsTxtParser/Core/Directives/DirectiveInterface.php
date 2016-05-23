<?php
namespace vipnytt\RobotsTxtParser\Core\Directives;

/**
 * Interface DirectiveInterface
 *
 * @package vipnytt\RobotsTxtParser\Core\Directives
 */
interface DirectiveInterface
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
