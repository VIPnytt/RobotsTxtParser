<?php
namespace vipnytt\RobotsTxtParser\Core;

/**
 * Class CharacterEncodingConvert
 *
 * @package vipnytt\RobotsTxtParser\Core
 */
class CharacterEncodingConvert implements RobotsTxtInterface
{
    /**
     * String to convert
     * @var string
     */
    protected $string;

    /**
     * Source encoding
     * @var string
     */
    protected $source;

    /**
     * CharacterEncodingConvert constructor.
     *
     * @param string $string
     * @param string $source
     */
    public function __construct($string, $source)
    {
        $this->string = $string;
        $this->source = $source;
    }

    /**
     * Auto mode
     *
     * @return string|false
     */
    public function auto()
    {
        if ($this->source == self::ENCODING) {
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
            $converted = iconv($this->source, self::ENCODING . $outSuffix, $this->string);
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
            $converted = mb_convert_encoding($this->string, self::ENCODING, $fromOverride === null ? $this->source : $fromOverride);
        } catch (\Exception $msg) {
            return false;
        }
        mb_internal_encoding(self::ENCODING);
        return $converted;
    }
}
