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
     * @param string $rule
     */
    public function __construct($rule, $parent = null);

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
