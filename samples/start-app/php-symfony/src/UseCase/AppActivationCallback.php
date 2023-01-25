<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Entity\Token;
use App\Repository\TokenRepository;
use GuzzleHttp\ClientInterface;


final class AppActivationCallback
{
    const GET_APP_TOKEN_URL = '/connect/apps/v1/oauth2/token';

    public function __construct(
        private readonly ClientInterface $client,
        private readonly TokenRepository $tokenRepository,
        private readonly string          $oauthClientId,
        private readonly string          $oauthClientSecret)
    {
    }

    public function execute(array $session, string $state, string $code): array
    {
        // We check if the received state is the same as in the session, for security.
        $sessionState = $session['oauth2_state'] ?? '';
        $state = $state ?? '';
        if (empty($state) || $state !== $sessionState) {
            exit('Invalid state');
        }

        $authorizationCode = $code ?? '';
        if (empty($authorizationCode)) {
            exit('Missing authorization code');
        }

        $pimUrl = $session['pim_url'] ?? '';
        if (empty($pimUrl)) {
            exit('No PIM url in session');
        }

        $codeIdentifier = bin2hex(random_bytes(30));
        $codeChallenge = hash('sha256', $codeIdentifier . $this->oauthClientSecret);

        $accessTokenUrl = $pimUrl . self::GET_APP_TOKEN_URL;
        $accessTokenRequestPayload = [
            'client_id' => $this->oauthClientId,
            'code_identifier' => $codeIdentifier,
            'code_challenge' => $codeChallenge,
            'code' => $authorizationCode,
            'grant_type' => 'authorization_code',
        ];

        $response = $this->client->post($accessTokenUrl, ['form_params' => $accessTokenRequestPayload]);

        $contents = json_decode($response->getBody()->getContents(), true);

        $this->tokenRepository->save(Token::create($contents['access_token']), true);

        return $contents;
    }
}
