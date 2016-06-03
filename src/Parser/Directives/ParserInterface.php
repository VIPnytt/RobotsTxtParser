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
     * Rule array
     *
     * @return array
     */
    public function getRules();

    /**
     * Render
     *
     * @return string[]
     */
    public function render();
}
