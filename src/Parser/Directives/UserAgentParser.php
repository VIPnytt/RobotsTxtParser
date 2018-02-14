<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\Client\Directives\UserAgentTools;
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
        self::DIRECTIVE_NO_INDEX => 'noIndex',
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
     * UserAgent constructor.
     *
     * @param string $base
     * @param string $effective
     */
    public function __construct($base, $effective)
    {
        $this->base = $base;
        $this->effective = $effective;
        $this->handlerAdd(self::USER_AGENT);
    }

    /**
     * Add sub-directive handler
     *
     * @param string $group
     * @return bool
     */
    private function handlerAdd($group)
    {
        if (!in_array($group, array_keys($this->handler))) {
            $this->handler[$group] = new SubDirectiveHandler($this->base, $this->effective, $group);
            return true;
        }
        return false;
    }

    /**
     * Add line
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        if ($line == '' ||
            ($pair = $this->generateRulePair($line, [-1 => self::DIRECTIVE_USER_AGENT] + array_keys(self::SUB_DIRECTIVES))) === false) {
            return $this->append = false;
        }
        if ($pair[0] === self::DIRECTIVE_USER_AGENT) {
            return $this->set($pair[1]);
        }
        $this->append = false;
        $result = [];
        foreach ($this->current as $group) {
            $result[] = $this->handler[$group]->{self::SUB_DIRECTIVES[$pair[0]]}->add($pair[1]);
            $this->handler[$group]->count++;
        }
        return in_array(true, $result, true);
    }

    /**
     * Set new User-agent
     *
     * @param string $group
     * @return bool
     */
    private function set($group)
    {
        if (!$this->append) {
            $this->current = [];
        }
        $group = mb_strtolower($group);
        $this->current[] = $group;
        $this->handlerAdd($group);
        $this->append = true;
        return true;
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        return $handler->getLevel() == 2 ? $this->renderExtensive($handler) : $this->renderCompressed($handler);
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
        $list = array_keys($this->handler);
        sort($list);
        return $list;
    }

    /**
     * Add sub-directives to the RenderHandler
     *
     * @param string $userAgent
     * @param RenderHandler $handler
     */
    private function renderAdd($userAgent, RenderHandler $handler)
    {
        if ($userAgent !== self::USER_AGENT &&
            $this->handler[$userAgent]->count === 0
        ) {
            $handler->add(self::DIRECTIVE_DISALLOW, '');
            return;
        }
        $this->handler[$userAgent]->robotVersion->render($handler);
        $this->handler[$userAgent]->visitTime->render($handler);
        $this->handler[$userAgent]->noIndex->render($handler);
        $this->handler[$userAgent]->disallow->render($handler);
        $this->handler[$userAgent]->allow->render($handler);
        $this->handler[$userAgent]->crawlDelay->render($handler);
        $this->handler[$userAgent]->cacheDelay->render($handler);
        $this->handler[$userAgent]->requestRate->render($handler);
        $this->handler[$userAgent]->comment->render($handler);
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
        foreach ($this->getUserAgents() as $group) {
            if ($group == self::USER_AGENT &&
                $this->handler[$group]->count === 0
            ) {
                continue;
            }
            $array[$group] = (new UserAgentTools($this->handler[$group], $this->base))->export();
        }
        return $array;
    }

    /**
     * Client
     *
     * @param string $product
     * @param float|int|string|null $version
     * @param int|null $statusCode
     * @return UserAgentClient
     */
    public function client($product = self::USER_AGENT, $version = null, $statusCode = null)
    {
        $parser = new UserAgentStringParser($product, $version);
        $match = $parser->getUserAgent();
        if (!isset($this->handler[$match])) {
            // User-agent does not match any rule sets
            if (($match = $parser->getMostSpecific($this->getUserAgents())) === false) {
                $match = self::USER_AGENT;
            }
        }
        return new UserAgentClient($this->handler[$match], $this->base, $statusCode, $parser->getProduct());
    }
}
