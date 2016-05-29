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
        } elseif (($iconv = $this->iconv()) !== false) {
            return $iconv;
        } elseif (($mbstring = $this->mbstring()) !== false) {
            return $mbstring;
        }
        return false;
    }

    /**
     * iconv
     *
     * @param string $outSuffix
     * @return string|false
     */
    public function iconv($outSuffix = '//TRANSLIT//IGNORE')
    {
        try {
            $converted = iconv($this->encoding, self::ENCODING . $outSuffix, $this->string);
        } catch (\Exception $msg) {
            return false;
        }
        mb_internal_encoding(self::ENCODING);
        return $converted;
    }

    /**
     * mbstring
     *
     * @param array|string|null $fromOverride
     * @return string|false
     */
    public function mbstring($fromOverride = null)
    {
        try {
            $converted = mb_convert_encoding($this->string, self::ENCODING, $fromOverride === null ? $this->encoding : $fromOverride);
        } catch (\Exception $msg) {
            return false;
        }
        mb_internal_encoding(self::ENCODING);
        return $converted;
    }
}
