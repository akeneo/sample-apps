<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exception\UserNotFoundException;
use App\Repository\TokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\UseCase\AppActivationCallback;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppActivationCallbackController extends AbstractController
{
    public function __construct(
        private readonly AppActivationCallback    $callback,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UserRepositoryInterface  $userRepository,
        private readonly LoggerInterface          $logger,
        private readonly string                   $projectDir,
        private readonly string                   $openIdAuthentication)
    {
    }

    #[Route('/callback', name: 'callback', methods: ['GET'])]
    public function __invoke(): Response
    {
        session_start();

        try{
            $data = $this->callback->execute($_SESSION, $_GET['state'], $_GET['code']);

            $response = new Response();

            $divToReplace = "<div>UserInformation</div>";
            $divUserInformation = "";

            if (!$this->tokenRepository->hasToken()) {
                return $response->setContent(
                    str_replace(
                        $divToReplace,
                        $divUserInformation,
                        file_get_contents($this->projectDir . '/templates/no_access_token.html')
                    )
                );
            }

            if ($this->openIdAuthentication && isset($data['id_token'])) {
                $response = $this->forward('App\Controller\OpenIdController::extractUserInformation', [
                        'idToken' => $data['id_token']
                    ]
                );

                $user = $this->userRepository->getUserFromCookies($response->headers->getCookies());

                $divUserInformation = "<div class='userInformation'>"
                    . "<div>User : " . $user->getFirstname() . " " . $user->getLastname() . "</div>"
                    . "<div>Email : " . $user->getEmail() . "</div>"
                    . "</div>";
            }

            return $response->setContent(
                str_replace(
                    $divToReplace,
                    $divUserInformation,
                    file_get_contents($this->projectDir . '/templates/access_token.html')
                )
            );

        } catch (UserNotFoundException) {
            $divToReplace = "<div>UserInformation</div>";
            $divUserInformation = "<div class='userInformation'><div>Not connected</div></div>";
            return new Response(
                str_replace(
                    $divToReplace,
                    $divUserInformation,
                    file_get_contents($this->projectDir . '/templates/access_token.html')
                )
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return new Response(file_get_contents($this->projectDir . '/templates/error.html'));
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return new Response(file_get_contents($this->projectDir . '/templates/error.html'));
        }
    }
}

