<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\AppActivation;
use App\UseCase\NotifyAuthorizationUpdate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

final class NotifyAuthorizationUpdateController extends AbstractController
{
    public function __construct(private readonly NotifyAuthorizationUpdate $useCase)
    {
    }

    #[Route('/notify-authorization-update', name: 'notify_authorization_update', methods: ['GET'])]
    public function __invoke(): Response
    {
        $response = $this->useCase->execute(AppActivation::OAUTH_SCOPES);

        if ($response->getStatusCode() !== 200) {
            throw new HttpException($response->getStatusCode(), $response->getBody()->getContents());
        }

        return $this->redirect('/');
    }
}
