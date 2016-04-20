<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

/**
 * Interface DirectiveInterface
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
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
     * Export
     *
     * @return mixed
     */
    public function export();
}
