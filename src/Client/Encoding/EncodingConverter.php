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
        if ($this->encoding == self::ENCODING) {
            return $this->string;
        } elseif (
            extension_loaded('intl') &&
            ($intl = $this->intl()) !== false
        ) {
            return $intl;
        } elseif (
            extension_loaded('iconv') &&
            ($iconv = $this->iconv()) !== false
        ) {
            return $iconv;
        } elseif (($mbstring = $this->mbstring()) !== false) {
            return $mbstring;
        }
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
}
