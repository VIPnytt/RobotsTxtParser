<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

use Composer\CaBundle\CaBundle;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class UriClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/UriClient.md for documentation
 * @package vipnytt\RobotsTxtParser
 */
class UriClient extends TxtClient
{
    /**
     * User-agent
     */
    const CURL_USER_AGENT = 'RobotsTxtParser-VIPnytt/2.1 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)';

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
    private $effective;

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
        $uriParser = new UriParser($baseUri);
        $this->base = $uriParser->base();
        if ($this->request($curlOptions) === false) {
            $this->time = time();
            $this->effective = $this->base;
            $this->rawStatusCode = null;
            $this->rawContents = '';
            $this->rawEncoding = self::ENCODING;
            $this->rawMaxAge = 0;
        }
        parent::__construct($this->base, $this->rawStatusCode, $this->rawContents, $this->rawEncoding, $this->effective, $byteLimit);
    }

    /**
     * cURL request
     *
     * @param array $options
     * @return bool
     */
    private function request($options = [])
    {
        $curl = curl_init();
        $caPathOrFile = CaBundle::getSystemCaRootBundlePath();
        // Set default cURL options
        curl_setopt_array($curl, [
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_ENCODING => 'identity',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => self::CURL_USER_AGENT,
            (is_dir($caPathOrFile) ||
                (
                    is_link($caPathOrFile) &&
                    is_dir(readlink($caPathOrFile))
                )
            ) ? CURLOPT_CAPATH : CURLOPT_CAINFO => $caPathOrFile
        ]);
        // Apply custom cURL options
        curl_setopt_array($curl, $options);
        // Initialize the header parser
        $this->headerParser = new Parser\HeaderParser($curl);
        // Make sure these cURL options stays untouched
        curl_setopt_array($curl, [
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FTPSSLAUTH => CURLFTPAUTH_DEFAULT,
            CURLOPT_HEADER => false,
            CURLOPT_HEADERFUNCTION => [$this->headerParser, 'curlCallback'],
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_NOBODY => false,
            CURLOPT_PROTOCOLS => CURLPROTO_FTP | CURLPROTO_FTPS | CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_SFTP,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_FTP | CURLPROTO_FTPS | CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_SFTP,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->base . self::PATH,
            CURLOPT_USERPWD => 'anonymous:anonymous@',
        ]);
        // Execute cURL request
        if (($this->rawContents = curl_exec($curl)) === false) {
            // Request failed
            return false;
        }
        $this->time = time();
        $this->rawStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // also works with FTP status codes
        $uriParser = new UriParser(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));
        $this->effective = $uriParser->base();
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
     * @return string|null
     */
    public function getEffectiveUri()
    {
        return $this->effective;
    }

    /**
     * Status code
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        $statusCodeParser = new StatusCodeParser($this->rawStatusCode, parse_url($this->effective, PHP_URL_SCHEME));
        return $statusCodeParser->isValid() ? $this->rawStatusCode : null;
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
        if ($this->rawStatusCode === 503 &&
            strpos($this->base, 'http') === 0
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
