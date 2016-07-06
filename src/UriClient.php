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
    private $rawStatusCode;

    /**
     * Effective uri
     * @var string
     */
    private $rawEffectiveUri;

    /**
     * Cache-Control max-age
     * @var int
     */
    private $rawMaxAge;

    /**
     * Robots.txt contents
     * @var string
     */
    private $rawContents;

    /**
     * Robots.txt character encoding
     * @var string
     */
    private $rawEncoding;

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
            $this->rawEffectiveUri = $this->base . self::PATH;
            $this->rawStatusCode = null;
            $this->rawContents = '';
            $this->rawEncoding = self::ENCODING;
            $this->rawMaxAge = 0;
        }
        parent::__construct($this->base, $this->rawStatusCode, $this->rawContents, $this->rawEncoding, $this->rawEffectiveUri, $byteLimit);
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
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CAINFO => CaBundle::getSystemCaRootBundlePath(),
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_ENCODING => 'identity',
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FTPSSLAUTH => CURLFTPAUTH_DEFAULT,
            CURLOPT_HEADER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_NOBODY => false,
            CURLOPT_PROTOCOLS => CURLPROTO_FTP | CURLPROTO_FTPS | CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_SFTP,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_FTP | CURLPROTO_FTPS | CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_SFTP,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            //CURLOPT_SSL_VERIFYSTATUS => true, // PHP 7.0.7
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => self::CURL_USER_AGENT,
            CURLOPT_USERPWD => 'anonymous:anonymous@',
        ]);
        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, [$this->headerParser, 'curlCallback']);
        curl_setopt($curl, CURLOPT_URL, $this->base . self::PATH);
        if (($this->rawContents = curl_exec($curl)) === false) {
            return false;
        }
        $this->time = time();
        $this->rawStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // also works with FTP status codes
        $this->rawEffectiveUri = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        curl_close($curl);
        $this->rawEncoding = $this->headerParser->getCharset();
        $this->rawMaxAge = $this->headerParser->getMaxAge();
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
        return $this->rawEffectiveUri;
    }

    /**
     * Status code
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->rawStatusCode;
    }

    /**
     * Body content
     *
     * @return string
     */
    public function getContents()
    {
        return $this->rawContents;
    }

    /**
     * Encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->rawEncoding;
    }

    /**
     * Next update timestamp
     *
     * @return int
     */
    public function nextUpdate()
    {
        if (
            $this->rawStatusCode === 503 &&
            stripos($this->base, 'http') === 0
        ) {
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
        return $this->time + max(self::CACHE_TIME, $this->rawMaxAge);
    }
}
