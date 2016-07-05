<?php
namespace vipnytt\RobotsTxtParser;

use Composer\CaBundle\CaBundle;

/**
 * Class UriClient
 *
 * @package vipnytt\RobotsTxtParser
 */
class UriClient extends TxtClient
{
    /**
     * User-agent
     */
    const CURL_USER_AGENT = 'RobotsTxtParser-VIPnytt/2.0 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)';

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Header parser
     * @var Parser\HeaderParser
     */
    private $headerParser;

    /**
     * RequestClient timestamp
     * @var int
     */
    private $time;

    /**
     * Status code
     * @var int|null
     */
    private $statusCode;

    /**
     * Effective uri
     * @var string
     */
    private $effectiveUri;

    /**
     * Cache-Control max-age
     * @var int
     */
    private $maxAge;

    /**
     * Robots.txt contents
     * @var string
     */
    private $contents;

    /**
     * Robots.txt character encoding
     * @var string
     */
    private $encoding;

    /**
     * RequestClient constructor.
     *
     * @param string $baseUri
     * @param array $curlOptions
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, array $curlOptions = [], $byteLimit = self::BYTE_LIMIT)
    {
        $this->base = $this->urlBase($this->urlEncode($baseUri));
        if ($this->request($curlOptions) === false) {
            $this->time = time();
            $this->effectiveUri = $this->base . self::PATH;
            $this->statusCode = null;
            $this->contents = '';
            $this->encoding = self::ENCODING;
            $this->maxAge = 0;
        }
        parent::__construct($this->base, $this->statusCode, $this->contents, $this->encoding, $this->effectiveUri, $byteLimit);
    }

    /**
     * cURL request
     *
     * @param array $options
     * @return bool
     */
    private function request($options = [])
    {
        $this->headerParser = new Parser\HeaderParser();
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CAINFO => CaBundle::getSystemCaRootBundlePath(),
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_ENCODING => 'identity',
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FTPSSLAUTH => CURLFTPAUTH_DEFAULT,
            CURLOPT_HEADER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER,
            CURLOPT_NOBODY => false,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            //CURLOPT_SSL_VERIFYSTATUS => true, // PHP 7.0.7
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => self::CURL_USER_AGENT,
            CURLOPT_USERPWD => 'anonymous:',
        ]);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this->headerParser, 'curlCallback']);
        curl_setopt($ch, CURLOPT_URL, $this->base . self::PATH);
        if (($this->contents = curl_exec($ch)) === false) {
            return false;
        }
        $this->time = time();
        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // also works with FTP status codes
        $this->effectiveUri = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        $this->encoding = $this->headerParser->getCharset();
        $this->maxAge = $this->headerParser->getMaxAge();
        return true;
    }

    /**
     * Base uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->base;
    }

    /**
     * Effective uri
     *
     * @return string
     */
    public function getEffectiveUri()
    {
        return $this->effectiveUri;
    }

    /**
     * Status code
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Body content
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Next update timestamp
     *
     * @return int
     */
    public function nextUpdate()
    {
        if ($this->statusCode === 503) {
            return $this->time + min(self::CACHE_TIME, $this->headerParser->getRetryAfter($this->time));
        }
        return $this->time + self::CACHE_TIME;
    }

    /**
     * Valid until timestamp
     *
     * @return int
     */
    public function validUntil()
    {
        return $this->time + max(self::CACHE_TIME, $this->maxAge);
    }
}
