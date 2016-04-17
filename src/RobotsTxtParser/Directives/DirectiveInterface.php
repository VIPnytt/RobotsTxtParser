<?php
namespace vipnytt\RobotsTxtParser\Directives;

/**
 * Interface DirectiveInterface
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
interface DirectiveInterface
{
    /**
     * Constructor
     *
     * @param string $parent
     */
    public function __construct($parent = null);

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line);

    /**
     * Export
     *
     * @return mixed
     */
    public function export();
}
