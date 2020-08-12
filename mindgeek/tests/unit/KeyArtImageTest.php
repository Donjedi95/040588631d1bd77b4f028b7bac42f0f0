<?php

namespace App\Tests\unit;

use App\Entity\CacheImage\KeyArtImage;
use App\Service\Cache;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;

class KeyArtImageTest extends Unit
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
        $keyArtImage = new KeyArtImage($cacheService, $movie);

        $this->assertEquals('url', $keyArtImage->getImageUrlKey());
    }

    /**
     * @throws Exception
     */
    public function testGetCacheKey(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);
        $movie = [
            'id' => 'some_id_007'

        ];
        $cardImage = new KeyArtImage($cacheService, $movie);

        $this->assertEquals(
            'some_id_007_keyArtImage_some_image_007',
            $cardImage->getCacheKey('some_image_007')
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
            'id' => 'some_id_007'

        ];
        $cardImage = new KeyArtImage($cacheService, $movie);

        $this->assertEquals(
            'images/some_id_007/keyArtImages',
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
            'id' => 'some_id_007',
            KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY => [
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'some_keyArtImage_url1',
                    'h' => 123,
                    'w' => 123
                ],
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'some_keyArtImage_url2',
                    'h' => 123,
                    'w' => 123
                ],
                [
                    KeyArtImage::KEY_ART_IMAGE_URL_KEY => 'some_keyArtImage_url3',
                    'h' => 123,
                    'w' => 123
                ]
            ]
        ];
        $keyArtImage = new KeyArtImage($cacheService, $movie);

        $result = $keyArtImage->processImage();
        foreach ($result[KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY] as $keyArtImage) {
            $this->assertEquals('', $keyArtImage[KeyArtImage::KEY_ART_IMAGE_URL_KEY]);
        }
    }
}
