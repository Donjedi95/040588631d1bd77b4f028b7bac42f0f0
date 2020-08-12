<?php

namespace App\Tests\unit;

use App\Entity\CacheImage\GalleryThumbnail;
use App\Service\Cache;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;

class GalleryThumbnailTest extends Unit
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
        $galleryThumbnail = new GalleryThumbnail($cacheService, $movie);

        $this->assertEquals('thumbnailUrl', $galleryThumbnail->getImageUrlKey());
    }

    /**
     * @throws Exception
     */
    public function testGetCacheKey(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);
        $movie = [
            'id' => 'some_id_321'

        ];
        $cardImage = new GalleryThumbnail($cacheService, $movie);

        $this->assertEquals(
            'some_id_321_galleryThumbnail_some_image_321',
            $cardImage->getCacheKey('some_image_321')
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
            'id' => 'some_id_321'

        ];
        $cardImage = new GalleryThumbnail($cacheService, $movie);

        $this->assertEquals(
            'images/some_id_321/galleryThumbnails',
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
            'id' => 'some_id_100',
            GalleryThumbnail::GALLERIES_ARRAY_KEY => [
                [
                    'title' => 'some title 100',
                    'url' => 'a gallery url 100',
                    GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY => 'some_thumbnail_url',
                    'id' => 'some_gallery_id'
                ]
            ]
        ];
        $galleryThumbnail = new GalleryThumbnail($cacheService, $movie);

        $result = $galleryThumbnail->processImage();
        foreach ($result[GalleryThumbnail::GALLERIES_ARRAY_KEY] as $gallery) {
            $this->assertEquals('', $gallery[GalleryThumbnail::GALLERY_THUMBNAIL_URL_KEY]);
        }
    }
}
