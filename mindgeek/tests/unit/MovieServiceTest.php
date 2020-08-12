<?php

namespace App\Tests\unit;

use Exception;
use App\Entity\CacheImage\CardImage;
use App\Entity\CacheImage\GalleryThumbnail;
use App\Entity\CacheImage\KeyArtImage;
use App\Entity\CacheImage\VideoThumbnail;
use App\Service\Cache;
use App\Service\Mindgeek;
use App\Service\Movie;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MovieServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testGetMovies(): void
    {
        /** @var MockObject|Movie $movieService */
        $movieService = $this->make(Movie::class,
            [
                'cache' => $this->make(Cache::class),
                'mindgeekService' => $this->make(
                    Mindgeek::class,
                    [
                        'fetchShowcaseJson' => function () {
                            return [
                                [
                                    'id' => 'some_123_id'
                                ]
                            ];
                        }
                    ]
                )
            ]
        );

        $this->assertEquals(
            [
                'some_123_id' => [
                    'id' => 'some_123_id'
                ]
            ],
            $movieService->getMovies()
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testGetLazyMovies(): void
    {
        /** @var MockObject|Movie $movieService */
        $movieService = $this->make(Movie::class,
            [
                'cache' => $this->make(Cache::class),
                'getMovies' => function () {
                    return [
                        'someId_1' => [
                            'id' => 'someId_1',
                            KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY => [
                                [
                                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'some_url'
                                ]
                            ]
                        ],
                    ];
                },
                'getProcessedMovie' => function() {
                    return [
                        'id' => 'someId_1',
                        KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY => [
                            [
                                KeyArtImage::KEY_ART_IMAGE_URL_KEY => ''
                            ]
                        ]
                    ];
                }
            ]
        );

        $this->assertEquals(
            [
                'someId_1' => [
                    'id' => 'someId_1',
                    KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY => [
                        [
                            'url' => '/placeholder.jpg',
                            'width' => 500,
                            'height' => 500
                        ]
                    ]
                ],
            ],
            $movieService->getLazyMovies()
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testProcessMovieInfo(): void
    {
        /** @var MockObject|Movie $movieService */
        $movieService = $this->make(Movie::class,
            [
                'cache' => $this->make(Cache::class,
                    [
                        'getCachedImage' => function () {
                            return '';
                        }
                    ]
                )
            ]
        );

        $movie = [
            'id' => 'random_id',
            CardImage::CARD_IMAGES_ARRAY_KEY => [
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'url1'
                ],
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'url2'
                ],
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'url3'
                ]
            ],
            KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY => [
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'url4'
                ],
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'url5'
                ],
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'url6'
                ]
            ],
            VideoThumbnail::VIDEOS_ARRAY_KEY => [
                [
                    VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY => 'url7'
                ],
                [
                    VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY => 'url8'
                ],
                [
                    VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY => 'url9'
                ]
            ],
            GalleryThumbnail::GALLERIES_ARRAY_KEY => [
                [
                    GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY => 'url10'
                ],
                [
                    GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY => 'url11'
                ],
                [
                    GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY => 'url12'
                ]
            ]
        ];

        $result = $movieService->processMovieInfo($movie);

        foreach ($result[CardImage::CARD_IMAGES_ARRAY_KEY] as $cardImage) {
            $this->assertEquals('', $cardImage[CardImage::CARD_IMAGE_URL_KEY]);
        }
        foreach ($result[KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY] as $cardImage) {
            $this->assertEquals('', $cardImage[KeyArtImage::KEY_ART_IMAGE_URL_KEY]);
        }
        foreach ($result[VideoThumbnail::VIDEOS_ARRAY_KEY] as $cardImage) {
            $this->assertEquals('', $cardImage[VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY]);
        }
        foreach ($result[GalleryThumbnail::GALLERIES_ARRAY_KEY] as $cardImage) {
            $this->assertEquals('', $cardImage[GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY]);
        }
    }
}
