<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;

/**
 * Class CleanParamParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CleanParamParser extends CleanParamParserCore
{
    /**
     * Client cache
     * @var CleanParamClient
     */
    private $client;

    /**
     * CleanParamParser constructor.
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
        if (isset($this->client)) {
            return $this->client;
        }
        return $this->client = new CleanParamClient($this->cleanParam);
    }
}
