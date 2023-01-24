<?php

namespace App\Controller;

use App\UseCase\AppActivationCallback;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppActivationCallbackController extends AbstractController
{
    public function __construct(private readonly AppActivationCallback $callback)
    {
    }

    #[Route('/callback', name: 'callback', methods: ['GET'])]
    public function __invoke(): Response
    {
        session_start();
        $response = $this->callback->execute($_SESSION, $_GET['state'], $_GET['code']);

        return new JsonResponse($response);
    }
}

