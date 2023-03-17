<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exception\UserNotFoundException;
use App\Repository\TokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UserRepositoryInterface  $userRepository,
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

        $divToReplace="<div>UserInformation</div>";
        $divUserInformation="";
        try {
            $user = $this->userRepository->getUser();
            $divUserInformation = "<div class='userInformation'>"
                . "<div>User : " . $user->getFirstname() . " " . $user->getLastname() . "</div>"
                . "<div>Email : " . $user->getEmail() . "</div>"
                . "</div>";
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
