<?php

namespace App\Service;

use Exception;
use App\AppInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Mindgeek
{
    use FileDownloaderTrait;

    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private AdapterInterface $cache;
    private ParameterBagInterface $parameterBag;

    public const SHOWCASE_URL = 'https://mgtechtest.blob.core.windows.net/files/showcase.json';

    protected const ALLOWED_IMAGE_TYPES = [
        'jpg',
        'png',
        'jpeg',
    ];

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, AdapterInterface $cache, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function fetchShowcaseJson(): array
    {
        try {
            $jsonResponse = $this->fetchShowcaseResponseContent();
        } catch (Exception $exception) {
            $item = $this->cache->getItem(AppCacheInterface::CACHE_SHOWCASE_JSON_KEY);
            if (!$item->isHit()) {
                throw new Exception('Timeout and no cache');
            }

            $jsonResponse = $item->get();
        }

        // If i get a 500 Internal Server Error to have a cached response to not break the website
        $item = $this->cache->getItem(AppCacheInterface::CACHE_SHOWCASE_JSON_KEY);
        $item->set($jsonResponse);
        $this->cache->save($item);

        /** @noinspection PhpComposerExtensionStubsInspection */
        return json_decode($jsonResponse, true, 512, JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchShowcaseResponseContent(): string
    {
        // To increase app speed if response takes longer than 1 second, use the last saved jsonResponse
        ini_set('default_socket_timeout', AppInterface::SHOWCASE_JSON_DEFAULT_SOCKET_TIMEOUT);
        return $this->client->request('GET',self::SHOWCASE_URL)->getContent();
    }

    /**
     * @param array $image
     * @param string $directory
     * @return string
     * @throws Exception
     */
    public function getImageFromUrl(array $image, string $directory): string
    {
        ini_set('default_socket_timeout', AppInterface::IMAGE_DOWNLOAD_DEFAULT_SOCKET_TIMEOUT);
        $publicPath = $directory;

        $directory = $this->parameterBag->get('images_folder') . $directory;
        $this->createNewDirectory($directory);

        $parts = explode('/', $image['url']);
        $imageName = $parts[count($parts) - 1];

        $fullPath = $directory . '/' . $imageName;
        if (!$this->checkFileExists($fullPath)) {
            $contents = null;
            $this->logger->info("Getting image {$imageName}");

            try {
                $contents = $this->getContentsFromUrl($image['url']);
            } catch (Exception $exception) {
                $this->logger->warning($exception->getMessage());
            }

            if ($contents) {
                $this->saveContents($contents, $imageName, $fullPath, self::ALLOWED_IMAGE_TYPES);
                $this->logger->info("Saved {$imageName}");
            } else {
                $this->logger->info("Could not save {$imageName}");
                return '';
            }
        } else {
            $this->logger->info("Image already found on the server for {$imageName}");
        }

        return $publicPath . '/' . $imageName;
    }
}
