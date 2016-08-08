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
    use RobotsTxtParser\Handler\PhpAddOnTrait;

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
                                'path' => [
                                    '/private',
                                ],
                            ],
                            'disallow' => [
                                'hosts' => [
                                    'www.example.com',
                                ],
                                'cleanparam' => [
                                    'token' => [
                                        '/public/users',
                                    ],
                                    'uid' => [
                                        '/public/users',
                                    ],
                                ],
                                '/admin/',
                            ],
                            'allow' => [
                                'path' => [
                                    '/public',
                                ],
                            ],
                            'crawl-delay' => 'five',
                            'cache-delay' => 'ten',
                            'request-rate' => [
                                [
                                    'rate' => 9,
                                    'from' => '0900',
                                    'to' => '1500',
                                ],
                                [
                                    'rate' => 3.6,
                                    'from' => '0900',
                                ],
                            ],
                            'comment' => [
                                'Please honor the robots.txt rules. Thanks!'
                            ],
                        ],
                        'googlebot' => [
                            'disallow' => [
                                'path' => [
                                    '/',
                                ],
                            ],
                        ],
                        'bingbot' => [
                            'disallow' => [
                                'path' => [
                                    '/',
                                ],
                            ],
                        ],
                        'duckduckgo' => [
                            'disallow' => [
                                'path' => [
                                    '/',
                                ],
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
                            'noindex' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'disallow' => [
                                'host' => [],
                                'path' =>
                                    [],
                                'clean-param' => [],
                            ],
                            'allow' => [
                                'host' => [],
                                'path' =>
                                    [
                                        '/public',
                                    ],
                                'clean-param' => [],
                            ],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [
                                [
                                    'rate' => 9,
                                    'from' => '0900',
                                    'to' => '1500',
                                ],
                                [
                                    'rate' => 3.6,
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
                            'noindex' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'disallow' => [
                                'host' => [],
                                'path' => [
                                    '/',
                                ],
                                'clean-param' => [],
                            ],
                            'allow' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                        'bingbot' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'noindex' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'disallow' => [
                                'host' => [],
                                'path' => [
                                    '/',
                                ],
                                'clean-param' => [],
                            ],
                            'allow' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                        'duckduckgo' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'noindex' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
                            'disallow' => [
                                'host' => [],
                                'path' => [
                                    '/',
                                ],
                                'clean-param' => [],
                            ],
                            'allow' => [
                                'host' => [],
                                'path' => [],
                                'clean-param' => [],
                            ],
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
                            'disallow' => [
                                '/admin/',
                                'cleanparam' => [
                                    'token' => [
                                        '/public/users',
                                    ],
                                    'uid' => [
                                        '/public/users',
                                    ],
                                ],
                                'hosts' => [
                                    'www.example.com',
                                ],
                            ],
                            'index' => [
                                'path' => [
                                    '/private',
                                ],
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
