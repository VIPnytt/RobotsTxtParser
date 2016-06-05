<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

/**
 * Interface ParserInterface
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
     * Client
     *
     * @return object
     */
    public function client();

    /**
     * Render
     *
     * @return string[]
     */
    public function render();
}
