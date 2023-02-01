<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\AppActivation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppActivationController extends AbstractController
{
    public function __construct(private readonly AppActivation $useCase)
    {
    }

    #[Route('/activate', name: 'activate', methods: ['GET'])]
    public function __invoke(): Response
    {
        session_start();
        return $this->redirect($this->useCase->execute($_SESSION, $_GET['pim_url']));
    }
}
