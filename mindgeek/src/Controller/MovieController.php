<?php

namespace App\Controller;

use App\Service\Movie;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MovieController extends AbstractController
{
    /** @Route("/movie/{$id}", name="app_movie")
     * @param Movie $movieService
     * @param string id
     * @return Response
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     */
    public function show(Movie $movieService, string $id)
    {
        $movie = $movieService->getMovies()[$id];
        $movie = $movieService->processMovieInfo($movie);

        return $this->render(
            'site/movie.html.twig',
            [
                'title' => 'Mindgeek - ' . $movie['headline'],
                'movie' => $movie
            ]
        );
    }
}