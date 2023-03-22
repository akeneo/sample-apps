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
            $divToReplace = "<div>UserInformation</div>";
            $divUserInformation = "";

            if (!$this->tokenRepository->hasToken()) {
                return new Response(
                    str_replace(
                        $divToReplace,
                        $divUserInformation,
                        file_get_contents($this->projectDir . '/templates/no_access_token.html')
                    )
                );
            }

            if ($this->openIdAuthentication
                && $request->cookies->get('sub') != ''
                && $request->cookies->get('vector') != ''
            ) {
                $user = $this->userRepository->getUserFromCookies([
                    new Cookie('sub', $request->cookies->get('sub')),
                    new Cookie('vector', $request->cookies->get('vector')),
                ]);

                $divUserInformation = "<div class='userInformation'>"
                    . "<div>User : " . $user->getFirstname() . " " . $user->getLastname() . "</div>"
                    . "<div>Email : " . $user->getEmail() . "</div>"
                    . "</div>";
            }
        } catch (UserNotFoundException) {
            $divUserInformation = "<div class='userInformation'><div>Not connected</div></div>";
        }

        return new Response(
            str_replace(
                $divToReplace,
                $divUserInformation,
                file_get_contents($this->projectDir . '/templates/access_token.html')
            )
        );
    }
}
