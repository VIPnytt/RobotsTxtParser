<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\Directives\SubDirectiveHandler;

/**
 * Class UserAgentClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class UserAgentClient extends UserAgentTools
{
    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * UserAgentClient constructor.
     *
     * @param SubDirectiveHandler $handler
     * @param string $baseUri
     * @param int|null $statusCode
     * @param string $userAgent
     */
    public function __construct(SubDirectiveHandler $handler, $baseUri, $statusCode, $userAgent)
    {
        $this->userAgent = $userAgent;
        parent::__construct($handler, $baseUri, $statusCode);
    }

    /**
     * Allow
     *
     * @return AllowClient
     */
    public function allow()
    {
        return $this->handler->allow()->client();
    }

    /**
     * Cache-delay
     *
     * @return DelayClient
     */
    public function cacheDelay()
    {
        return $this->handler->cacheDelay()->client($this->userAgent, $this->crawlDelay()->getValue());
    }

    /**
     * Crawl-delay
     *
     * @return DelayClient
     */
    public function crawlDelay()
    {
        return $this->handler->crawlDelay()->client($this->userAgent);
    }

    /**
     * RequestClient-rate
     *
     * @return RequestRateClient
     */
    public function requestRate()
    {
        return $this->handler->requestRate()->client($this->userAgent, $this->crawlDelay()->getValue());
    }

    /**
     * Comment
     *
     * @return CommentClient
     */
    public function comment()
    {
        return $this->handler->comment()->client();
    }

    /**
     * Disallow
     *
     * @return AllowClient
     */
    public function disallow()
    {
        return $this->handler->disallow()->client();
    }

    /**
     * Robot-version
     *
     * @return RobotVersionClient
     */
    public function robotVersion()
    {
        return $this->handler->robotVersion()->client();
    }

    /**
     * Visit-time
     *
     * @return VisitTimeClient
     */
    public function visitTime()
    {
        return $this->handler->visitTime()->client();
    }
}
