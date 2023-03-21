<?php

namespace App\Controller;

use App\UseCase\OpenIdConnect;
use App\Entity\Exception\SessionInformationException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class OpenIdController extends AbstractController
{

    public function __construct(
        private readonly OpenIdConnect $openId)
    {
    }

    /**
     * @throws SessionInformationException
     * @throws GuzzleException
     */
    public function extractUserInformation(string $idToken): Response
    {
        $response = new Response();
        list($sub, $vector) = $this->openId->execute($_SESSION, $idToken);

        // Cookies are available for 90 days
        $subCookie = new Cookie('sub', $sub, time() + ( 90 * 24 * 60 *60));
        $vectorCookie = new Cookie('vector', $vector, time() + ( 90 * 24 * 60 *60));

        $response->headers->setCookie($subCookie);
        $response->headers->setCookie($vectorCookie);

        return $response;
    }
}
