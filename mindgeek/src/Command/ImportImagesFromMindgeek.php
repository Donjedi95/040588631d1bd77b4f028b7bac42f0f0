<?php

namespace App\Command;

use App\Entity\CacheImage\KeyArtImage;
use App\Service\Cache;
use App\Service\Mindgeek;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ImportImagesFromMindgeek extends Command
{
    private ParameterBagInterface $parameterBag;
    private Mindgeek $mindgeekService;
    private Cache $cacheService;

    protected static $defaultName = 'app:import:mindgeek:images';

    public function __construct(ParameterBagInterface $parameterBag, Mindgeek $mindgeekService, Cache $cacheService)
    {
        $this->parameterBag = $parameterBag;
        $this->mindgeekService = $mindgeekService;
        $this->cacheService = $cacheService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Imports the keyArtImages from Mindgeek endpoint');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('default_socket_timeout', $this->parameterBag->get('image_download_command_socket_timeout'));

        $output->writeln('============================================================');
        $output->writeln('Starting import from ' . $this->mindgeekService::SHOWCASE_URL);
        $output->writeln('============================================================');

        $movies = $this->mindgeekService->fetchShowcaseJson();
        foreach ($movies as $movie) {
            $output->writeln('Importing keyArtImages for movie: ' . $movie['headline']);
            (new KeyArtImage($this->cacheService, $movie))->processImage();
        }

        $output->writeln('============================================================');
        $output->writeln('IMPORT DONE!');
        $output->writeln('============================================================');

        return Command::SUCCESS;
    }
}
