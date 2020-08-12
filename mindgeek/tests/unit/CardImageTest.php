<?php

namespace App\Tests\unit;

use App\Entity\CacheImage\CardImage;
use App\Service\Cache;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;

class CardImageTest extends Unit
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
        $cardImage = new CardImage($cacheService, $movie);

        $this->assertEquals('url', $cardImage->getImageUrlKey());
    }

    /**
     * @throws Exception
     */
    public function testGetCacheKey(): void
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class);
        $movie = [
            'id' => 'some_id_123'

        ];
        $cardImage = new CardImage($cacheService, $movie);

        $this->assertEquals(
            'some_id_123_cardImage_some_image_123',
            $cardImage->getCacheKey('some_image_123')
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
            'id' => 'some_id_123'

        ];
        $cardImage = new CardImage($cacheService, $movie);

        $this->assertEquals(
                'images/some_id_123/cardImages',
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
            'id' => 'some_id_123',
            CardImage::CARD_IMAGES_ARRAY_KEY => [
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'someUrl1',
                    'h' => 123,
                    'w' => 123
                ],
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'someUrl2',
                    'h' => 123,
                    'w' => 123
                ],
                [
                    CardImage::CARD_IMAGE_URL_KEY => 'someUrl3',
                    'h' => 123,
                    'w' => 123
                ]
            ]
        ];
        $cardImage = new CardImage($cacheService, $movie);

        $result = $cardImage->processImage();
        foreach ($result[CardImage::CARD_IMAGES_ARRAY_KEY] as $cardImage) {
            $this->assertEquals('', $cardImage[CardImage::CARD_IMAGE_URL_KEY]);
        }
    }
}
