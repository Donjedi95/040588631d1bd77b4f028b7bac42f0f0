<?php

namespace App\Controller;

use App\Service\Mindgeek;
use App\Service\Movie;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class IndexController extends AbstractController
{
    /** @Route("/", name="app_homepage")
     * @param Movie $service
     * @return Response
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     */
    public function index(Movie $service): Response
    {
        return $this->render(
            'site/index.html.twig',
            [
                'title' => 'Mindgeek Homepage',
                'content' => $service->getLazyMovies()
            ]
        );
    }
}