<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\UserAgentParser as UAStringParser;

/**
 * Class UserAgentParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class UserAgentParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Sub directives white list
     */
    const SUB_DIRECTIVES = [
        self::DIRECTIVE_ALLOW => 'allow',
        self::DIRECTIVE_CACHE_DELAY => 'cacheDelay',
        self::DIRECTIVE_COMMENT => 'comment',
        self::DIRECTIVE_CRAWL_DELAY => 'crawlDelay',
        self::DIRECTIVE_DISALLOW => 'disallow',
        self::DIRECTIVE_REQUEST_RATE => 'requestRate',
        self::DIRECTIVE_ROBOT_VERSION => 'robotVersion',
        self::DIRECTIVE_VISIT_TIME => 'visitTime',
    ];

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_USER_AGENT;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * User-agent handler
     * @var SubDirectiveHandler[]
     */
    private $handler = [];

    /**
     * User-agent(s)
     * @var string[]
     */
    private $userAgent = [self::USER_AGENT];

    /**
     * User-agent client cache
     * @var UserAgentClient
     */
    private $client;

    /**
     * UserAgent constructor.
     *
     * @param string $base
     */
    public function __construct($base)
    {
        $this->base = $base;
        $this->set();
    }

    /**
     * Set new User-agent
     *
     * @param array $array
     * @return bool
     */
    public function set(array $array = [self::USER_AGENT])
    {
        $this->userAgent = array_map('mb_strtolower', $array);
        foreach ($this->userAgent as $userAgent) {
            if (!in_array($userAgent, array_keys($this->handler))) {
                $this->handler[$userAgent] = new SubDirectiveHandler($this->base, $userAgent);
            }
        }
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
        $result = [];
        if (($pair = $this->generateRulePair($line, array_keys(self::SUB_DIRECTIVES))) === false) {
            return false;
        }
        foreach ($this->userAgent as $userAgent) {
            $result[] = $this->handler[$userAgent]->{self::SUB_DIRECTIVES[$pair['directive']]}()->add($pair['value']);
        }
        return in_array(true, $result, true);
    }

    /**
     * Client
     *
     * @param string $userAgent
     * @param int|null $statusCode
     * @return UserAgentClient
     */
    public function client($userAgent = self::USER_AGENT, $statusCode = null)
    {
        if (isset($this->client[$userAgent])) {
            return $this->client[$userAgent];
        }
        $userAgent = mb_strtolower($userAgent);
        $userAgentParser = new UAStringParser($userAgent);
        if (($userAgentMatch = $userAgentParser->match($this->getUserAgents())) === false) {
            $userAgentMatch = self::USER_AGENT;
        }
        return $this->client[$userAgent] = new UserAgentClient($this->handler[$userAgentMatch], $this->base, $statusCode);
    }

    /**
     * User-agent list
     *
     * @return string[]
     */
    public function getUserAgents()
    {
        return array_keys($this->handler);
    }

    /**
     * Rule array
     *
     * @return array
     */
    public function getRules()
    {
        $result = [];
        foreach ($this->getUserAgents() as $userAgent) {
            $current = array_merge(
                $this->handler[$userAgent]->robotVersion()->getRules(),
                $this->handler[$userAgent]->visitTime()->getRules(),
                $this->handler[$userAgent]->disallow()->getRules(),
                $this->handler[$userAgent]->allow()->getRules(),
                $this->handler[$userAgent]->crawlDelay()->getRules(),
                $this->handler[$userAgent]->cacheDelay()->getRules(),
                $this->handler[$userAgent]->requestRate()->getRules(),
                $this->handler[$userAgent]->comment()->getRules()
            );
            if (!empty($current)) {
                $result[$userAgent] = $current;
            }
        }
        return empty($result) ? [] : [self::DIRECTIVE => $result];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $userAgents = $this->getUserAgents();
        sort($userAgents);
        $result = [];
        foreach ($userAgents as $userAgent) {
            $current = array_merge(
                $this->handler[$userAgent]->robotVersion()->render(),
                $this->handler[$userAgent]->visitTime()->render(),
                $this->handler[$userAgent]->disallow()->render(),
                $this->handler[$userAgent]->allow()->render(),
                $this->handler[$userAgent]->crawlDelay()->render(),
                $this->handler[$userAgent]->cacheDelay()->render(),
                $this->handler[$userAgent]->requestRate()->render(),
                $this->handler[$userAgent]->comment()->render()
            );
            if (!empty($current)) {
                $result = array_merge($result, [self::DIRECTIVE . ':' . $userAgent], $current);
            }
        }
        return $result;
    }
}
