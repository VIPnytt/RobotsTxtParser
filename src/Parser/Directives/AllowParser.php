<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\AllowClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class AllowParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class AllowParser implements ParserInterface, RobotsTxtInterface
{
    /**
     * Directive
     * @var string
     */
    private $directive;

    /**
     * Path
     * @var array
     */
    private $path = [];

    /**
     * Sort result
     * @var bool
     */
    private $sort = false;

    /**
     * AllowParser constructor
     *
     * @param string $base
     * @param string $effective
     * @param string $directive
     */
    public function __construct($base, $effective, $directive)
    {
        $this->directive = $directive;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $line = rtrim($line, '*');
        if (in_array(substr($line, 0, 1), [
                '/',
                '*',
                '?',
            ]) &&
            !in_array($line, $this->path)
        ) {
            $this->path[] = $line;
            return true;
        }
        return false;
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        if ($this->directive === self::DIRECTIVE_DISALLOW &&
            count($this->path) === 0 &&
            $handler->getLevel() == 2
        ) {
            $handler->add($this->directive, '');
            return true;
        }
        $this->sort();
        foreach ($this->path as $path) {
            $handler->add($this->directive, $path);
        }
        return true;
    }

    /**
     * Sort by length
     *
     * @return bool
     */
    private function sort()
    {
        if ($this->sort) {
            return $this->sort;
        };
        return $this->sort = rsort($this->path) && usort($this->path, function ($a, $b) {
                // PHP 7: Switch to the <=> "Spaceship" operator
                return mb_strlen($a) - mb_strlen($b);
            });
    }

    /**
     * Client
     *
     * @return AllowClient
     */
    public function client()
    {
        $this->sort();
        return new AllowClient($this->path);
    }
}
