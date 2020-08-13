<?php

namespace App\Tests\unit;

use App\Service\Cache;
use App\Service\Mindgeek;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\Log\Logger;

class CacheServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function testGetCachedImage()
    {
        /** @var MockObject|Cache $cacheService */
        $cacheService = $this->make(Cache::class, [
            'cache' => $this->make(FilesystemAdapter::class, [
                'get' => function () {
                    return '';
                },
            ]),
            'mindgeekService' => $this->make(Mindgeek::class, [
                'getImageFromUrl' => function () {
                    return '';
                }
            ]),
            'deleteCache' => function () {
                return true;
            },
            'logger' => new Logger()
        ]);

        $key = '_uTest_' . md5(rand());
        $imageData = [
            'url' => 'someUrl',
            'w' => '123',
            'h' => '123'
        ];
        $id = 'some-movie-id';
        $directory = 'images';

        $this->assertEquals('', $cacheService->getCachedImage($key, $imageData, $id, $directory));
        $this->assertTrue($cacheService->deleteCache($key));
    }
}
