<?php

namespace App\Controller;

use App\UseCase\FirstApiCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FirstApiCallController extends AbstractController
{
    public function __construct(private readonly FirstApiCall $useCase)
    {
    }

    #[Route('/first-api-call', name: 'first_api_call', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new JsonResponse($this->useCase->execute());
    }
}