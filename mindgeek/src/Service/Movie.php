<?php

namespace App\Service;

use App\Entity\AbstractCacheImage;
use App\Entity\CacheImage\CardImage;
use App\Entity\CacheImage\GalleryThumbnail;
use App\Entity\CacheImage\KeyArtImage;
use App\Entity\CacheImage\VideoThumbnail;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Movie
{
    private Cache $cache;
    private Mindgeek $mindgeekService;

    public function __construct(Cache $cache, Mindgeek $mindgeekService)
    {
        $this->cache = $cache ;
        $this->mindgeekService = $mindgeekService;
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMovies(): array
    {
        $movies = [];
        foreach ($this->mindgeekService->fetchShowcaseJson() as $key => $movie) {
            $movies[$movie['id']] = $movie;
        }

        return $movies;
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getLazyMovies(): array
    {
        $movies = $this->getMovies();
        foreach ($movies as &$movie) {
            $keyArtImage = new KeyArtImage($this->cache, $movie);
            $movie = $this->getProcessedMovie($keyArtImage);

            if (!$keyArtImage->hasAnImage()) {
                $movie[KeyArtImage::KEY_ART_IMAGES_ARRAY_KEY] = [
                    [
                        'url' => '/placeholder.jpg',
                        'width' => 500,
                        'height' => 500
                    ]
                ];
            }
        }

        return $movies;
    }

    /**
     * @param $movie
     * @return array
     * @throws InvalidArgumentException
     */
    public function processMovieInfo($movie): array
    {
        $movie = (new CardImage($this->cache, $movie))->processImage();
        $movie = (new KeyArtImage($this->cache, $movie))->processImage();
        $movie = (new VideoThumbnail($this->cache, $movie))->processImage();
        $movie = (new GalleryThumbnail($this->cache, $movie))->processImage();

        return $movie;
    }

    /**
     * @param AbstractCacheImage $image
     * @return array
     * @throws InvalidArgumentException
     */
    protected function getProcessedMovie(AbstractCacheImage $image) {
        return $image->processImage();
    }
}
