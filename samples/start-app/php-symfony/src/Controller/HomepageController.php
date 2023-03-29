<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exception\UserNotFoundException;
use App\Repository\TokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    use ResponseTrait;

    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UserRepositoryInterface  $userRepository,
        private readonly string                   $projectDir,
        private readonly string                   $openIdAuthentication
    )
    {
    }

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        try {
            if (!$this->tokenRepository->hasToken()) {
                return new Response(
                    file_get_contents($this->projectDir . '/templates/no_access_token.html')
                );
            }

            $user=null;
            if ($this->openIdAuthentication
                && $request->cookies->get('sub') != ''
                && $request->cookies->get('vector') != ''
            ) {
                $user = $this->userRepository->getUserFromCookies([
                    new Cookie('sub', $request->cookies->get('sub')),
                    new Cookie('vector', $request->cookies->get('vector')),
                ]);
            }
            return new Response(
                $this->getResponseContent(
                    $this->projectDir . '/templates/access_token.html',
                    $user
                )
            );
        } catch (UserNotFoundException) {
            return new Response(
                $this->getResponseContent(
                    $this->projectDir . '/templates/access_token.html'
                )
            );
        }
    }
}
