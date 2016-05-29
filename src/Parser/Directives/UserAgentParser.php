<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

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
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_COMMENT,
        self::DIRECTIVE_CRAWL_DELAY,
        self::DIRECTIVE_DISALLOW,
        self::DIRECTIVE_REQUEST_RATE,
        self::DIRECTIVE_ROBOT_VERSION,
        self::DIRECTIVE_VISIT_TIME,
    ];

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_USER_AGENT;

    /**
     * All User-agents declared
     * @var array
     */
    public $userAgents = [];

    /**
     * Sub-directive Allow
     * @var DisAllowParser[]
     */
    public $allow = [];

    /**
     * Sub-directive Cache-delay
     * @var CrawlDelayParser[]
     */
    public $cacheDelay = [];

    /**
     * Sub-directive Comment
     * @var CommentParser[]
     */
    public $comment = [];

    /**
     * Sub-directive Crawl-delay
     * @var CrawlDelayParser[]
     */
    public $crawlDelay = [];

    /**
     * Sub-directive Disallow
     * @var DisAllowParser[]
     */
    public $disallow = [];

    /**
     * Sub-directive RequestClient-rate
     * @var RequestRateParser[]
     */
    public $requestRate = [];

    /**
     * Sub-directive Robot-version
     * @var RobotVersionParser[]
     */
    public $robotVersion = [];

    /**
     * Sub-directive Visit-time
     * @var VisitTimeParser[]
     */
    public $visitTime = [];

    /**
     * Current User-agent(s)
     * @var array
     */
    protected $userAgent = [];

    /**
     * UserAgent constructor.
     */
    public function __construct()
    {
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
            if (!in_array($userAgent, $this->userAgents)) {
                $this->allow[$userAgent] = new DisAllowParser(self::DIRECTIVE_ALLOW);
                $this->cacheDelay[$userAgent] = new CrawlDelayParser(self::DIRECTIVE_CACHE_DELAY);
                $this->comment[$userAgent] = new CommentParser();
                $this->crawlDelay[$userAgent] = new CrawlDelayParser(self::DIRECTIVE_CRAWL_DELAY);
                $this->disallow[$userAgent] = new DisAllowParser(self::DIRECTIVE_DISALLOW);
                $this->requestRate[$userAgent] = new RequestRateParser();
                $this->robotVersion[$userAgent] = new RobotVersionParser();
                $this->visitTime[$userAgent] = new VisitTimeParser();
                $this->userAgents[] = $userAgent;
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
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        foreach ($this->userAgent as $userAgent) {
            switch ($pair['directive']) {
                case self::DIRECTIVE_ALLOW:
                    $result[] = $this->allow[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CACHE_DELAY:
                    $result[] = $this->cacheDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_COMMENT:
                    $result[] = $this->comment[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CRAWL_DELAY:
                    $result[] = $this->crawlDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_DISALLOW:
                    $result[] = $this->disallow[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_REQUEST_RATE:
                    $result[] = $this->requestRate[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_ROBOT_VERSION:
                    $result[] = $this->robotVersion[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_VISIT_TIME:
                    $result[] = $this->visitTime[$userAgent]->add($pair['value']);
                    break;
            }
        }
        return in_array(true, $result, true);
    }

    /**
     * Export rules
     *
     * @return array
     */
    public function export()
    {
        $result = [];
        foreach ($this->userAgents as $userAgent) {
            $current = array_merge(
                $this->allow[$userAgent]->export(),
                $this->comment[$userAgent]->export(),
                $this->cacheDelay[$userAgent]->export(),
                $this->crawlDelay[$userAgent]->export(),
                $this->disallow[$userAgent]->export(),
                $this->requestRate[$userAgent]->export(),
                $this->robotVersion[$userAgent]->export(),
                $this->visitTime[$userAgent]->export()
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
        $result = [];
        sort($this->userAgents);
        foreach ($this->userAgents as $userAgent) {
            $current = array_merge(
                $this->allow[$userAgent]->render(),
                $this->comment[$userAgent]->render(),
                $this->cacheDelay[$userAgent]->render(),
                $this->crawlDelay[$userAgent]->render(),
                $this->disallow[$userAgent]->render(),
                $this->requestRate[$userAgent]->render(),
                $this->robotVersion[$userAgent]->render(),
                $this->visitTime[$userAgent]->render()
            );
            if (!empty($current)) {
                $result = array_merge($result, [self::DIRECTIVE . ':' . $userAgent], $current);
            }
        }
        return $result;
    }
}
