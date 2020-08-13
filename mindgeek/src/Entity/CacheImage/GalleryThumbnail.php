<?php

namespace App\Entity\CacheImage;

use App\Entity\AbstractCacheImage;

class GalleryThumbnail extends AbstractCacheImage
{
    public const GALLERIES_ARRAY_KEY = 'galleries';
    public const GALLERY_THUMBNAIL_URL_KEY = 'thumbnailUrl';
    public const GALLERY_THUMBNAILS_DIRECTORY = 'images/{id}/galleryThumbnails';
    public const GALLERY_THUMBNAIL_CACHE_KEY = '{id}_galleryThumbnail_{imageName}';

    public function getImageUrlKey(): string
    {
        return self::GALLERY_THUMBNAIL_URL_KEY;
    }

    public function getArrayKey(): string
    {
        return self::GALLERIES_ARRAY_KEY;
    }

    public function getCacheKey($imageName): string
    {
        return strtr(
            self::GALLERY_THUMBNAIL_CACHE_KEY,
            [
                '{id}' => $this->movie['id'],
                '{imageName}' => $imageName
            ]
        );
    }

    public function getDirectory(): string
    {
        return strtr(
            self::GALLERY_THUMBNAILS_DIRECTORY,
            [
                '{id}' => $this->movie['id']
            ]
        );
    }
}
