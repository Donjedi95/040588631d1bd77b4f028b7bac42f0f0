<?php

namespace App\Tests\unit;

use App\Entity\CacheImage\VideoThumbnail;
use App\Service\Cache;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;

class VideoThumbnailTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @throws Exception
     */
    public function testGetImageUrlKey(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);

        $movie = [];
        $videoThumbnail = new VideoThumbnail($cacheService, $movie);

        $this->assertEquals('thumbnailUrl', $videoThumbnail->getImageUrlKey());
    }

    /**
     * @throws Exception
     */
    public function testGetCacheKey(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);
        $movie = [
            'id' => 'some_id_666'

        ];
        $videoThumbnails = new VideoThumbnail($cacheService, $movie);

        $this->assertEquals(
            'some_id_666_videoThumbnail_some_image_666.img',
            $videoThumbnails->getCacheKey('some_image_666.img')
        );
    }

    /**
     * @throws Exception
     */
    public function testGetDirectory()
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);

        $movie = [
            'id' => 'some_id_666'

        ];
        $cardImage = new VideoThumbnail($cacheService, $movie);

        $this->assertEquals(
            'images/some_id_666/videoThumbnails',
            $cardImage->getDirectory()
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testProcessImage(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class,
            [
                'getCachedImage' => function () {
                    return '';
                }
            ]
        );

        $movie = [
            'id' => 'some_id_666',
            VideoThumbnail::VIDEOS_ARRAY_KEY => [
                [
                    'title' => 'some video title',
                    'type' => 'some type',
                    VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY => 'some_image_666.jpg',
                    'url' => 'http://some_url.com'
                ]
            ]
        ];
        $videoThumbnail = new VideoThumbnail($cacheService, $movie);

        $result = $videoThumbnail->processImage();
        foreach ($result[VideoThumbnail::VIDEOS_ARRAY_KEY] as $video) {
            $this->assertEquals('', $video[VideoThumbnail::VIDEO_THUMBNAIL_URL_KEY]);
        }
    }
}
