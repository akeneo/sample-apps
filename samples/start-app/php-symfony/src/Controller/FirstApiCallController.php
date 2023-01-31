<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TokenRepositoryInterface;
use App\UseCase\FirstApiCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FirstApiCallController extends AbstractController
{
    public function __construct(
        private readonly FirstApiCall             $useCase,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly string                   $projectDir
    )
    {
    }

    #[Route('/first-api-call', name: 'first_api_call', methods: ['GET'])]
    public function __invoke(): Response
    {
        if (!$this->tokenRepository->hasToken()) {
            return new Response(file_get_contents($this->projectDir . '/templates/no_access_token.html'));
        }

        return new JsonResponse($this->useCase->execute());
    }
}
