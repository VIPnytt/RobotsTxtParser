<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Client\Directives\InlineCleanParamClient;

/**
 * Class InlineCleanParamParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class InlineCleanParamParser extends CleanParamParserCore
{
    /**
     * InlineCleanParamParser constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Client
     *
     * @return CleanParamClient
     */
    public function client()
    {
        return new InlineCleanParamClient($this->cleanParam);
    }
}
