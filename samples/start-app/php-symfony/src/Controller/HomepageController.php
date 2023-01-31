<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TokenRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly string                   $projectDir
    )
    {
    }

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function __invoke(): Response
    {
        if (!$this->tokenRepository->hasToken()) {
            return new Response(file_get_contents($this->projectDir . '/templates/no_access_token.html'));
        }

        return new Response(
            file_get_contents($this->projectDir . '/templates/access_token.html')
        );
    }
}
