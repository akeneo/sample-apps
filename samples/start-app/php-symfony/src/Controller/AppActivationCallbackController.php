<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exception\UserNotFoundException;
use App\Repository\TokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\UseCase\AppActivationCallback;
use App\UseCase\OpenIdConnect;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppActivationCallbackController extends AbstractController
{
    use ResponseTrait;

    public function __construct(
        private readonly AppActivationCallback    $callback,
        private readonly OpenIdConnect            $openId,
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

            $user = null;

            if (!$this->tokenRepository->hasToken()) {
                return $response->setContent(
                    file_get_contents($this->projectDir . '/templates/no_access_token.html')
                );
            }

            if ($this->openIdAuthentication && isset($data['id_token'])) {

                list($sub, $vector) = $this->openId->execute($_SESSION, $data['id_token']);

                // Cookies are available for 90 days
                $subCookie = new Cookie('sub', $sub, time() + ( 90 * 24 * 60 *60));
                $vectorCookie = new Cookie('vector', $vector, time() + ( 90 * 24 * 60 *60));

                $response->headers->setCookie($subCookie);
                $response->headers->setCookie($vectorCookie);

                $user = $this->userRepository->getUserFromCookies($response->headers->getCookies());
            }

            return $response->setContent(
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
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return new Response(file_get_contents($this->projectDir . '/templates/error.html'));
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return new Response(file_get_contents($this->projectDir . '/templates/error.html'));
        }
    }
}

