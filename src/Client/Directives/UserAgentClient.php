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
     * UserAgentClient constructor.
     *
     * @param SubDirectiveHandler $handler
     * @param string $baseUri
     * @param int|null $statusCode
     */
    public function __construct(SubDirectiveHandler $handler, $baseUri, $statusCode)
    {
        parent::__construct($handler, $baseUri, $statusCode);
    }

    /**
     * Allow
     *
     * @return DisAllowClient
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
        return $this->handler->cacheDelay()->client($this->crawlDelay()->get());
    }

    /**
     * Crawl-delay
     *
     * @return DelayClient
     */
    public function crawlDelay()
    {
        return $this->handler->crawlDelay()->client($this->requestRate()->get());
    }

    /**
     * RequestClient-rate
     *
     * @return RequestRateClient
     */
    public function requestRate()
    {
        return $this->handler->requestRate()->client();
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
     * @return DisAllowClient
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
