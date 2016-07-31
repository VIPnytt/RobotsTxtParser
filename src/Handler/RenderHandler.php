<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RenderHandler
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
class RenderHandler implements RobotsTxtInterface
{
    /**
     * Mode
     * @var int
     */
    private $mode;

    /**
     * Line separator
     * @var string
     */
    private $separator;

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
     * @param int $mode
     * @param string $lineSeparator
     */
    public function __construct($mode, $lineSeparator = "\r\n")
    {
        $this->mode = $mode;
        $this->separatorCheck($lineSeparator);
    }

    /**
     * Line separator check
     *
     * @param string $lineSeparator
     * @throws ClientException
     */
    private function separatorCheck($lineSeparator)
    {
        if (!in_array($lineSeparator, [
            "\r\n",
            "\n",
            "\r",
        ])
        ) {
            throw new ClientException('Invalid line separator');
        }
        $this->separator = $lineSeparator;
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Implode
     *
     * @param string $directive
     * @param RenderHandler $handler
     * @return bool
     */
    public function addInline($directive, RenderHandler $handler)
    {
        foreach ($handler->export() as $line) {
            $this->add($directive, trim($line));
        }
        return true;
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
        if ($this->mode >= 2) {
            $this->strings[] = $this->advanced() . $line;
            return true;
        }
        $this->strings[] = $this->basic() . $line;
        return true;
    }

    /**
     * Advanced beautifier
     *
     * @return string
     */
    private function advanced()
    {
        $result = '';
        if ($this->boolInsertSeparatorMode3() ||
            $this->previous !== self::DIRECTIVE_USER_AGENT &&
            $this->directive === self::DIRECTIVE_USER_AGENT
        ) {
            $result = $this->separator;
            $this->previousRoot = $this->directive;
        }
        $result .= $this->basic();
        return $result;
    }

    /**
     * Should insert line separator? requires mode 3
     *
     * @return bool
     */
    private function boolInsertSeparatorMode3()
    {
        return $this->mode >= 3 &&
        $this->previousRoot !== $this->directive &&
        in_array($this->directive, [
            self::DIRECTIVE_HOST,
            self::DIRECTIVE_CLEAN_PARAM,
            self::DIRECTIVE_SITEMAP,
        ]);
    }

    /**
     * Basic beautifier
     *
     * @return string
     */
    private function basic()
    {
        $result = $this->directive . ':';
        if ($this->mode === 0) {
            return $result;
        }
        return ucfirst($result) . ' ';
    }

    /**
     * Generate robots.txt
     *
     * @return string
     */
    public function generate()
    {
        return trim(implode($this->separator, $this->strings));
    }
}
