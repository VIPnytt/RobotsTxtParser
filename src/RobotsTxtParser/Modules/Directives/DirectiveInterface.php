<?php
namespace vipnytt\RobotsTxtParser\Modules\Directives;

/**
 * Interface DirectiveInterface
 *
 * @package vipnytt\RobotsTxtParser\Modules\Directives
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
