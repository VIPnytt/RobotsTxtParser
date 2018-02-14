<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

use vipnytt\RobotsTxtParser\Parser\RobotsTxtParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RenderHandler
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
class RenderHandler implements RobotsTxtInterface
{
    /**
     * Render level
     * @var int
     */
    private $level;

    /**
     * Line separator
     * @var string
     */
    private $eol;

    /**
     * Rule strings
     * @var string[]
     */
    private $strings = [];

    /**
     * Current directive
     * @var string
     */
    private $directive;

    /**
     * Previous directive
     * @var string
     */
    private $previous;

    /**
     * Previous root level directive
     * @var string
     */
    private $previousRoot;

    /**
     * RenderHandler constructor.
     *
     * @param int $level
     * @param string $lineSeparator
     */
    public function __construct($level, $lineSeparator = "\r\n")
    {
        $this->level = $level;
        $this->separatorCheck($lineSeparator);
    }

    /**
     * Line separator check
     *
     * @param string $eol
     * @throws \InvalidArgumentException
     */
    private function separatorCheck($eol)
    {
        if (!in_array($eol, [
            "\r\n",
            "\n",
            "\r",
        ])
        ) {
            throw new \InvalidArgumentException('Invalid line separator');
        }
        $this->eol = $eol;
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        return $this->strings;
    }

    /**
     * Add line
     *
     * @param string $directive
     * @param string $line
     * @return bool
     */
    public function add($directive, $line)
    {
        $this->previous = $this->directive;
        $this->directive = $directive;
        $this->strings[] = rtrim($this->prefix() . $line);
        return true;
    }

    /**
     * Advanced beautifier
     *
     * @return string
     */
    private function prefix()
    {
        $result = $this->lineBreak() . $this->directive . ':';
        if ($this->level === 0) {
            return $result;
        }
        return ucwords($result) . ' ';
    }

    /**
     * Returns an line separator if required
     *
     * @return string
     */
    private function lineBreak()
    {
        if ($this->previousRoot !== $this->directive &&
            in_array($this->directive, array_keys(RobotsTxtParser::TOP_LEVEL_DIRECTIVES)) ||
            $this->previous !== self::DIRECTIVE_USER_AGENT &&
            $this->directive === self::DIRECTIVE_USER_AGENT
        ) {
            $this->previousRoot = $this->directive;
            if ($this->level >= 1) {
                return $this->eol;
            }
        }
        return '';
    }

    /**
     * Generate robots.txt
     *
     * @return string
     */
    public function generate()
    {
        return trim(implode($this->eol, $this->strings), $this->eol);
    }
}
