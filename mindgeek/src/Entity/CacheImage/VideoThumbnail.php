<?php

namespace App\Entity\CacheImage;

use App\Entity\AbstractCacheImage;

class VideoThumbnail extends AbstractCacheImage
{
    public const VIDEOS_ARRAY_KEY = 'videos';
    public const VIDEO_THUMBNAIL_URL_KEY = 'thumbnailUrl';
    public const VIDEO_THUMBNAILS_DIRECTORY = 'images/{id}/videoThumbnails';
    public const VIDEO_THUMBNAIL_CACHE_KEY = '{id}_videoThumbnail_{imageName}';

    public function getImageUrlKey(): string
    {
        return self::VIDEO_THUMBNAIL_URL_KEY;
    }

    public function getArrayKey(): string
    {
        return self::VIDEOS_ARRAY_KEY;
    }

    public function getCacheKey($imageName): string
    {
        return strtr(
            self::VIDEO_THUMBNAIL_CACHE_KEY,
            [
                '{id}' => $this->getMovieId(),
                '{imageName}' => $imageName
            ]
        );
    }

    public function getDirectory(): string
    {
        return strtr(
            self::VIDEO_THUMBNAILS_DIRECTORY,
            [
                '{id}' => $this->getMovieId()
            ]
        );
    }
}