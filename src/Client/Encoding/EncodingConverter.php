<?php
namespace vipnytt\RobotsTxtParser\Client\Encoding;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class EncodingConverter
 *
 * @package vipnytt\RobotsTxtParser\Client\Encoding
 */
class EncodingConverter implements RobotsTxtInterface
{
    /**
     * Errors
     * @var array
     */
    protected $errors = [];

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
     * CharacterEncodingConvert constructor.
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
        set_error_handler([$this, 'customErrorHandler'], E_NOTICE | E_WARNING);
        foreach ([
                     'intl',
                     'iconv',
                     'xml',
                     'mbstring',
                 ] as $extension
        ) {
            $last = end($this->errors);
            if (
                extension_loaded($extension) &&
                ($result = call_user_func([$this, $extension])) !== false &&
                $last === end($this->errors)
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
            $converted = $uConverter->convert($this->string);
        } catch (\Exception $e) {
            return false;
        }
        mb_internal_encoding(self::ENCODING);
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
        mb_internal_encoding(self::ENCODING);
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
        mb_internal_encoding(self::ENCODING);
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
        } catch (\Exception $e) {
            return false;
        }
        mb_internal_encoding(self::ENCODING);
        return $converted;
    }

    /**
     * Custom error handler
     *
     * @param int $errNo
     * @param string $errStr
     * @param string $errFile
     * @param string $errLine
     * @return bool
     */
    protected function customErrorHandler($errNo, $errStr, $errFile, $errLine)
    {
        switch ($errNo) {
            case E_NOTICE:
            case E_WARNING:
                $this->errors[] = "lvl: " . $errNo . " | msg:" . $errStr . " | file:" . $errFile . " | ln:" . $errLine;
                return true;
        }
        return false;
    }
}
