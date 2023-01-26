<?php

namespace App\Controller;

use App\Entity\Exception\NoAccessTokenException;
use App\Repository\TokenRepositoryInterface;
use App\UseCase\AppActivationCallback;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppActivationCallbackController extends AbstractController
{
    public function __construct(
        private readonly AppActivationCallback    $callback,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly string                   $projectDir)
    {
    }

    #[Route('/callback', name: 'callback', methods: ['GET'])]
    public function __invoke(): Response
    {
        session_start();
        $this->callback->execute($_SESSION, $_GET['state'], $_GET['code']);

        try {
            if ($this->tokenRepository->getToken()) {
                return new Response(
                    file_get_contents($this->projectDir . '/templates/access_token.html')
                );
            }
        } catch (NoAccessTokenException $e) {
        }

        return new Response(
            file_get_contents($this->projectDir . '/templates/no_access_token.html')
        );
    }
}

