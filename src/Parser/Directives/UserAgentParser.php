<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\Handler\Directives\SubDirectiveHandler;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\UserAgentParser as UserAgentStringParser;

/**
 * Class UserAgentParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class UserAgentParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserTrait;

    /**
     * Sub directives white list
     */
    const SUB_DIRECTIVES = [
        self::DIRECTIVE_ALLOW => 'allow',
        self::DIRECTIVE_CACHE_DELAY => 'cacheDelay',
        self::DIRECTIVE_COMMENT => 'comment',
        self::DIRECTIVE_CRAWL_DELAY => 'crawlDelay',
        self::DIRECTIVE_DISALLOW => 'disallow',
        self::DIRECTIVE_NO_INDEX => 'noindex',
        self::DIRECTIVE_REQUEST_RATE => 'requestRate',
        self::DIRECTIVE_ROBOT_VERSION => 'robotVersion',
        self::DIRECTIVE_VISIT_TIME => 'visitTime',
    ];

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Effective uri
     * @var string
     */
    private $effective;

    /**
     * User-agent handler
     * @var SubDirectiveHandler[]
     */
    private $handler = [];

    /**
     * Current User-agent(s)
     * @var string[]
     */
    private $current = [];

    /**
     * Append User-agent
     * @var bool
     */
    private $append = false;

    /**
     * User-agent directive count
     * @var int[]
     */
    private $count = [];

    /**
     * User-agent client cache
     * @var UserAgentClient[]
     */
    private $client = [];

    /**
     * UserAgent constructor.
     *
     * @param string $base
     * @param string $effective
     */
    public function __construct($base, $effective)
    {
        $this->base = $base;
        $this->effective = $effective;
        $this->set(self::USER_AGENT);
        $this->append = false;
    }

    /**
     * Set new User-agent
     *
     * @param string $userAgent
     * @return bool
     */
    private function set($userAgent)
    {
        if (!$this->append) {
            $this->current = [];
        }
        $userAgent = mb_strtolower($userAgent);
        if (in_array(self::USER_AGENT, array_merge($this->current, [$userAgent]))) {
            $this->current = [];
            $userAgent = self::USER_AGENT;
        }
        $this->current[] = $userAgent;
        if (!in_array($userAgent, array_keys($this->handler))) {
            $this->handler[$userAgent] = new SubDirectiveHandler($this->base, $this->effective, $userAgent);
            $this->count[$userAgent] = 0;
        }
        $this->append = true;
        return true;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        if (($pair = $this->generateRulePair($line, array_merge([self::DIRECTIVE_USER_AGENT], array_keys(self::SUB_DIRECTIVES)))) === false) {
            $this->append = false;
            return false;
        }
        if ($pair['directive'] === self::DIRECTIVE_USER_AGENT) {
            return $this->set($pair['value']);
        }
        $this->append = false;
        $result = [];
        foreach ($this->current as $userAgent) {
            $result[] = $this->handler[$userAgent]->{self::SUB_DIRECTIVES[$pair['directive']]}()->add($pair['value']);
            $this->count[$userAgent]++;
        }
        return in_array(true, $result, true);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        return $handler->getLevel() >= 3 ? $this->renderExtensive($handler) : $this->renderCompressed($handler);
    }

    /**
     * Render extensive
     *
     * @param RenderHandler $handler
     * @return bool
     */
    private function renderExtensive(RenderHandler $handler)
    {
        $userAgents = $this->getUserAgents();
        rsort($userAgents);
        foreach ($userAgents as $userAgent) {
            $handler->add(self::DIRECTIVE_USER_AGENT, $userAgent);
            $this->renderAdd($userAgent, $handler);
        }
        return true;
    }

    /**
     * User-agent list
     *
     * @return string[]
     */
    public function getUserAgents()
    {
        $list = array_keys(array_filter($this->count));
        sort($list);
        return $list;
    }

    private function renderAdd($userAgent, RenderHandler $handler)
    {
        $this->handler[$userAgent]->robotVersion()->render($handler);
        $this->handler[$userAgent]->visitTime()->render($handler);
        $this->handler[$userAgent]->noIndex()->render($handler);
        $this->handler[$userAgent]->disallow()->render($handler);
        $this->handler[$userAgent]->allow()->render($handler);
        $this->handler[$userAgent]->crawlDelay()->render($handler);
        $this->handler[$userAgent]->cacheDelay()->render($handler);
        $this->handler[$userAgent]->requestRate()->render($handler);
        $this->handler[$userAgent]->comment()->render($handler);
    }

    /**
     * Render compressed
     *
     * @param RenderHandler $handler
     * @return bool
     */
    private function renderCompressed(RenderHandler $handler)
    {
        $pair = $this->export();
        while (!empty($pair)) {
            $groupMembers = current($pair);
            foreach (array_keys($pair, $groupMembers) as $userAgent) {
                $handler->add(self::DIRECTIVE_USER_AGENT, $userAgent);
                unset($pair[$userAgent]);
            }
            if (isset($userAgent)) {
                $this->renderAdd($userAgent, $handler);
                unset($userAgent);
            }
        }
        return true;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        $array = [];
        foreach ($this->getUserAgents() as $userAgent) {
            $array[$userAgent] = $this->client($userAgent)->export();
        }
        return $array;
    }

    /**
     * Client
     *
     * @param string $product
     * @param int|string|null $version
     * @param int|null $statusCode
     * @return UserAgentClient
     */
    public function client($product = self::USER_AGENT, $version = null, $statusCode = null)
    {
        if (isset($this->client[$product . $version . $statusCode])) {
            return $this->client[$product . $version . $statusCode];
        }
        $userAgentProduct = rtrim($product . '/' . $version, '/');
        $userAgentMatch = $userAgentProduct;
        if (!isset($this->handler[$userAgentMatch])) {
            $userAgentParser = new UserAgentStringParser($product, $version);
            $userAgentProduct = $userAgentParser->getProduct();
            if (($userAgentMatch = $userAgentParser->getMostSpecific($this->getUserAgents())) === false) {
                $userAgentMatch = self::USER_AGENT;
            }
        }
        $client = new UserAgentClient($this->handler[$userAgentMatch], $this->base, $statusCode, $userAgentProduct);
        $this->client = [
            $product . $version . $statusCode => $client
        ];
        return $client;
    }
}
