<?php
namespace vipnytt\RobotsTxtParser\Parser;

/**
 * Class CharacterEncodingConvert
 *
 * @package vipnytt\RobotsTxtParser\Parser
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
     * Destination encoding
     * @var string
     */
    protected $destination;

    /**
     * CharacterEncodingConvert constructor.
     *
     * @param string $string
     * @param string $source
     * @param string $destination
     */
    public function __construct($string, $source, $destination = self::ENCODING)
    {
        $this->string = $string;
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * Auto mode
     *
     * @return string|false
     */
    public function auto()
    {
        if ($this->source == $this->destination) {
            return $this->string;
        } elseif (($iconv = $this->iconv()) !== false) {
            return $iconv;
        } elseif (($mbstring = $this->mbstring()) !== false) {
            return $mbstring;
        }
        $this->fallback();
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
            $converted = iconv($this->source, $this->destination . $outSuffix, $this->string);
        } catch (\Exception $msg) {
            return false;
        }
        mb_internal_encoding($this->destination);
        return $converted;
    }

    /**
     * mb_convert_encoding
     *
     * @param array|string|null $fromOverride
     * @return string|false
     */
    public function mbstring($fromOverride = null)
    {
        try {
            $converted = mb_convert_encoding($this->string, $this->destination, $fromOverride === null ? $this->source : $fromOverride);
        } catch (\Exception $msg) {
            return false;
        }
        mb_internal_encoding($this->destination);
        return $converted;
    }

    /**
     * mb_internal_encoding
     *
     * @return bool
     */
    protected function fallback()
    {
        try {
            mb_internal_encoding($this->destination);
        } catch (\Exception $msg) {
            mb_internal_encoding($this->source);
            return false;
        }
        return true;
    }
}
