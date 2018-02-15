<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class ImportTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class ImportTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param array $source
     * @param array $export
     * @param array $diff
     */
    public function testImport(array $source, array $export, array $diff)
    {
        $import = new RobotsTxtParser\Import($source, 'http://example.com');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Import', $import);

        $this->assertEquals($export, $import->export());
        $this->assertEquals($diff, $import->getIgnoredImportData());
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                [
                    'host' => 'example.com',
                    'sitemap' =>
                        [
                            'http://example.com/sitemap.xml',
                            'http://example.com/sitemap.xml.gz',
                        ],
                    'user-agent' => [
                        '*' => [
                            'visit-time' => [
                                [
                                    'from' => '0123',
                                    'to' => '2301',
                                ],
                            ],
                            'index' => [
                                '/private',
                            ],
                            'disallow' => [
                                '/admin/',
                            ],
                            'allow' => [
                                '/public',
                            ],
                            'crawl-delay' => 'five',
                            'cache-delay' => 'ten',
                            'request-rate' => [
                                [
                                    'rate' => 9,
                                    'ratio' => '1/9s',
                                    'from' => '0900',
                                    'to' => '1500',
                                ],
                                [
                                    'rate' => 3.6,
                                    'ratio' => '5/18s',
                                    'from' => '0900',
                                ],
                            ],
                            'comment' => [
                                'Please honor the robots.txt rules. Thanks!'
                            ],
                        ],
                        'googlebot' => [
                            'disallow' => [
                                '/',
                            ],
                        ],
                        'bingbot' => [
                            'disallow' => [
                                '/',
                            ],
                        ],
                        'duckduckgo' => [
                            'disallow' => [
                                '/',
                            ],
                        ],
                    ],
                ],
                [
                    'host' => 'example.com',
                    'clean-param' => [],
                    'sitemap' => [
                        'http://example.com/sitemap.xml',
                        'http://example.com/sitemap.xml.gz',
                    ],
                    'user-agent' => [
                        '*' => [
                            'robot-version' => null,
                            'visit-time' => [
                                [
                                    'from' => '0123',
                                    'to' => '2301',
                                ],
                            ],
                            'noindex' => [],
                            'disallow' => [
                                '/admin/',
                            ],
                            'allow' => [
                                '/public',
                            ],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [
                                [
                                    'rate' => 9,
                                    'ratio' => '1/9s',
                                    'from' => '0900',
                                    'to' => '1500',
                                ],
                                [
                                    'rate' => 3.6,
                                    'ratio' => '5/18s',
                                    'from' => null,
                                    'to' => null,
                                ],
                            ],
                            'comment' => [
                                'Please honor the robots.txt rules. Thanks!'
                            ],
                        ],
                        'googlebot' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'noindex' => [],
                            'disallow' => [
                                '/',
                            ],
                            'allow' => [],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                        'bingbot' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'noindex' => [],
                            'disallow' => [
                                '/',
                            ],
                            'allow' => [],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                        'duckduckgo' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'noindex' => [],
                            'disallow' => [
                                '/',
                            ],
                            'allow' => [],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                    ],
                ],
                [
                    'user-agent' => [
                        '*' => [
                            'cache-delay' => 'ten',
                            'crawl-delay' => 'five',
                            'index' => [
                                '/private',
                            ],
                            'request-rate' => [
                                1 => [
                                    'from' => '0900',
                                ],
                            ],
                        ],
                    ],
                ]
            ]
        ];
    }
}
