<?php
namespace vipnytt\RobotsTxtParser;

trait ObjectTools
{
    /**
     * Check basic rule
     *
     * @param string $path
     * @param array $paths
     * @return bool
     */
    public function checkPath($path, $paths)
    {
        // bug: https://github.com/t1gor/Robots.txt-Parser-Class/issues/62
        foreach ($paths as $robotPath) {
            $escaped = strtr($robotPath, ["@" => '\@']);
            if (preg_match('@' . $escaped . '@', $path)) {
                if (mb_stripos($escaped, '$') !== false) {
                    if (mb_strlen($escaped) - 1 == mb_strlen($path)) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generate directive/rule pair
     *
     * @param string $line
     * @param array $whiteList
     * @return array|false
     */
    protected function generateRulePair($line, $whiteList)
    {
        $whiteList = array_map('mb_strtolower', $whiteList);
        // Split by directive and rule
        $pair = array_map('trim', mb_split(':', $line, 2));
        // Check if the line contains a rule
        if (
            empty($pair[1]) ||
            empty($pair[0]) ||
            !in_array(($pair[0] = mb_strtolower($pair[0])), $whiteList)
        ) {
            // Line does not contain any supported directive
            return false;
        }
        return [
            'directive' => $pair[0],
            'value' => $pair[1],
        ];
    }

    /**
     * Get path
     *
     * @param string $url
     * @return string
     * @throws Exceptions\ClientException
     */
    protected function getPath($url)
    {
        $url = $this->urlEncode($url);
        if (mb_stripos($url, '/') === 0) {
            // URL already is a path
            return $url;
        }
        if (!$this->urlValidate($url)) {
            throw new Exceptions\ClientException('Invalid URL');
        }
        return parse_url($url, PHP_URL_PATH);
    }
}
