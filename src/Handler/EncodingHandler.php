<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class EncodingHandler
 *
 * @link http://www.ietf.org/rfc/rfc3986.txt
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
class EncodingHandler implements RobotsTxtInterface
{
    /**
     * String to convert
     * @var string
     */
    private $string;

    /**
     * String encoding
     * @var string
     */
    private $encoding;

    /**
     * EncodingHandler constructor.
     *
     * @param string $string
     * @param string $encoding
     */
    public function __construct($string, $encoding)
    {
        $this->string = $string;
        $this->encoding = $encoding;
    }

    /**
     * Auto mode
     *
     * @return string|false
     */
    public function auto()
    {
        if (strtoupper($this->encoding) === self::ENCODING) {
            return $this->string;
        }
        $errorHandler = new ErrorHandler();
        set_error_handler([$errorHandler, 'callback'], E_NOTICE | E_WARNING);
        foreach ([
                     'intl',
                     'iconv',
                     'xml',
                     'mbstring',
                 ] as $extension) {
            $last = $errorHandler->getLast();
            if (extension_loaded($extension) &&
                ($result = call_user_func([$this, $extension])) !== false &&
                $last === $errorHandler->getLast()
            ) {
                restore_error_handler();
                return $result;
            }
        }
        restore_error_handler();
        return false;
    }

    /**
     * intl
     * @link http://php.net/manual/en/uconverter.convert.php
     *
     * @return string|false
     */
    public function intl()
    {
        try {
            $uConverter = new \UConverter(self::ENCODING, $this->encoding);
            $converted = $uConverter->convert($this->string, false);
        } catch (\Exception $e) {
            return false;
        }
        return $converted;
    }

    /**
     * iconv
     * @link http://php.net/manual/en/function.iconv.php
     *
     * @param string $outSuffix
     * @return string|false
     */
    public function iconv($outSuffix = '//TRANSLIT//IGNORE')
    {
        try {
            $converted = iconv($this->encoding, self::ENCODING . $outSuffix, $this->string);
        } catch (\Exception $e) {
            return false;
        }
        return $converted;
    }

    /**
     * xml
     * @link http://php.net/manual/en/function.utf8-encode.php
     *
     * @return string|false
     */
    public function xml()
    {
        if (strtoupper($this->encoding) !== 'ISO-8859-1') {
            return false;
        }
        try {
            $converted = utf8_encode($this->string);
        } catch (\Exception $e) {
            return false;
        }
        return $converted;
    }

    /**
     * mbstring
     * @link http://php.net/manual/en/function.mb-convert-encoding.php
     *
     * @param array|string|null $fromOverride
     * @return string|false
     */
    public function mbstring($fromOverride = null)
    {
        try {
            $converted = mb_convert_encoding($this->string, self::ENCODING, $fromOverride === null ? $this->encoding : $fromOverride);
        } catch (\Throwable $e) {
            return false;
        }
        return $converted;
    }
}
