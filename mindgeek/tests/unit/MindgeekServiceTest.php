<?php

namespace App\Tests\unit;

use Exception;
use App\Service\Mindgeek;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MindgeekServiceTest extends Unit
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testFetchShowcaseResponse(): void
    {
        $body = json_encode(
            [
                [
                    'id' => 'some_id'
                ]
            ]
        );

        /** @var MockObject|Mindgeek $mindgeekService */
        $mindgeekService = $this->make(Mindgeek::class,
            [
                'client' => new MockHttpClient(new MockResponse($body, [])),
                'parameterBag' => $this->make(ParameterBag::class,
                    [
                        'get' => function () {
                            return 1;
                        }
                    ]
                )

            ]
        );

        $result = $mindgeekService->fetchShowcaseResponseContent();
        $this->assertEquals('[{"id":"some_id"}]', $result);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testFetchShowcaseJsonSuccess(): void
    {
        $cacheService = new FilesystemAdapter(
            'test',
            60,
            'var/cache/test'
        );

        /** @var MockObject|Mindgeek $mindgeekService */
        $mindgeekService = $this->make(Mindgeek::class,
            [
                'fetchShowcaseResponseContent' => function () {
                    return '[{"id":"some_id"}]';
                },
                'parameterBag' => $this->make(ParameterBag::class,
                    [
                        'get' => function () {
                            return 'some_key';
                        }
                    ]
                ),
                'cache' => $cacheService
            ],
        );

        $result = $mindgeekService->fetchShowcaseJson();
        $this->assertEquals(
            [
                [
                    'id' => 'some_id'
                ]
            ],
            $result
        );
        $this->assertTrue($cacheService->delete('123'));
    }

    public function setTestGetImageFromUrlData(): array
    {
        return [
            [
                'data' => [
                    'image' => [
                        'url' => 'some_img.jpg'
                    ],
                    'directory' => 'someDirectory',
                    'checkFileExists' => true
                ],
                'expected' => 'someDirectory/some_img.jpg'
            ],
            [
                'data' => [
                    'image' => [
                        'url' => 'another_img.jpg'
                    ],
                    'directory' => 'someDirectory',
                    'checkFileExists' => false,
                    'getContentsFromUrl' => 'content',
                    'saveContents' => true
                ],
                'expected' => 'someDirectory/another_img.jpg'
            ],
            [
                'data' => [
                    'image' => [
                        'url' => 'another_img123.jpg'
                    ],
                    'directory' => 'someDirectory',
                    'checkFileExists' => false,
                    'getContentsFromUrl' => false,
                    'saveContents' => true
                ],
                'expected' => ''
            ]
        ];
    }

    /**
     * @dataProvider setTestGetImageFromUrlData
     * @param $data
     * @param $expected
     * @throws Exception
     */
    public function testGetImageFromUrl($data, $expected): void
    {
        /** @var MockObject|Mindgeek $mindgeekService */
        $mindgeekService = $this->make(Mindgeek::class,
            [
                'createNewDirectory' => function () {},
                'checkFileExists' => function () use ($data) {
                    return $data['checkFileExists'];
                },
                'getContentsFromUrl' => function () use ($data) {
                    return $data['getContentsFromUrl'] ?? false;
                },
                'saveContents' => function () {},
                'logger' => $this->make(Logger::class,
                    [
                        'info' => function (){},
                        'warning' => function (){}
                    ]
                ),
                'parameterBag' => $this->make(ParameterBag::class,
                    [
                        'get' => function () {
                            return 'images';
                        }
                    ]
                )
            ],
        );

        $result = $mindgeekService->getImageFromUrl($data['image'], $data['directory']);
        $this->assertEquals($expected, $result);
    }
}
