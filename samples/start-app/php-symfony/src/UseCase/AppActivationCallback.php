<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Entity\Exception\AuthorizationCodeException;
use App\Entity\Exception\InvalidStateException;
use App\Entity\Exception\SessionInformationException;
use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;


final class AppActivationCallback
{
    const GET_APP_TOKEN_URL = '/connect/apps/v1/oauth2/token';
    const OPENID_PUBLIC_KEY = '/connect/apps/v1/openid/public-key';

    public function __construct(
        private readonly ClientInterface $client,
        private readonly TokenRepository $tokenRepository,
        private readonly UserRepository  $userRepository,
        private readonly string          $oauthClientId,
        private readonly string          $oauthClientSecret,
        private readonly string          $openIdAuthentication)
    {
    }

    /**
     * @throws InvalidStateException
     * @throws AuthorizationCodeException
     * @throws SessionInformationException
     * @throws GuzzleException
     */
    public function execute(array $session, string $state, string $code): void
    {
        // We check if the received state is the same as in the session, for security.
        $sessionState = $session['oauth2_state'] ?? '';
        $state = $state ?? '';
        if (empty($state) || $state !== $sessionState) {
            throw new InvalidStateException(InvalidStateException::INVALID_STATE);
        }

        $authorizationCode = $code ?? '';
        if (empty($authorizationCode)) {
            throw new AuthorizationCodeException(AuthorizationCodeException::MISSING_AUTH_CODE);
        }

        $pimUrl = $session['pim_url'] ?? '';
        if (empty($pimUrl)) {
            throw new SessionInformationException(SessionInformationException::MISSING_PIM_URL);
        }

        $codeIdentifier = bin2hex(random_bytes(30));
        $codeChallenge = hash('sha256', $codeIdentifier . $this->oauthClientSecret);

        $accessTokenUrl = sprintf($pimUrl.'%s', self::GET_APP_TOKEN_URL);
        $accessTokenRequestPayload = [
            'client_id' => $this->oauthClientId,
            'code_identifier' => $codeIdentifier,
            'code_challenge' => $codeChallenge,
            'code' => $authorizationCode,
            'grant_type' => 'authorization_code',
        ];

        $response = $this->client->post($accessTokenUrl, ['form_params' => $accessTokenRequestPayload]);

        $contents = json_decode($response->getBody()->getContents(), true);

        $this->tokenRepository->upsert(Token::create($contents['access_token']), true);

        if ($this->openIdAuthentication && isset($contents['id_token'])) {
            $this->extractAndUpsertOpenIdInformation($contents['id_token'], $pimUrl);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function extractAndUpsertOpenIdInformation($idToken, $pimUrl): void
    {
        $openIdPublicKey = $this->fetchOpenIdPublicKey($pimUrl);
        $claims = $this->extractClaimsFromSignedToken($idToken, $openIdPublicKey, $pimUrl);
        list($subId, $email, $firstname, $lastname) = $this->getUserProfileFromTokenClaims($claims);

        $this->userRepository->upsert(User::create($subId, $email, $firstname, $lastname), true);
    }

    /**
     * @throws GuzzleException
     */
    private function fetchOpenIdPublicKey(string $pimUrl): string
    {
        $openIDPublicKeyUrl = sprintf($pimUrl.'%s', self::OPENID_PUBLIC_KEY);

        $response = $this->client->get($openIDPublicKeyUrl);
        $contents = json_decode($response->getBody()->getContents(), true);
        if (!array_key_exists('public_key', $contents)) {
            throw new \LogicException('Failed to retrieve openid public key');
        }
        if (!is_string($contents['public_key'])) {
            throw new \LogicException('OpenID public key is not a string');
        }

        return $contents['public_key'];
    }

    private function extractClaimsFromSignedToken(string $idToken, string $signature, string $issuer): array
    {
        $jwtConfig = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($signature)
        );
        $token = $jwtConfig->parser()->parse($idToken);
        \assert($token instanceof UnencryptedToken);

        $jwtConfig->setValidationConstraints(
            new IssuedBy($issuer),
            new SignedWith(new Sha256(), InMemory::plainText($signature))
        );
        $constraints = $jwtConfig->validationConstraints();
        $jwtConfig->validator()->assert($token, ...$constraints);

        return $token->claims()->all();
    }

    private function getUserProfileFromTokenClaims(array $tokenClaims): array
    {
        if (!isset($tokenClaims['sub'], $tokenClaims['email'], $tokenClaims['firstname'], $tokenClaims['lastname'])) {
            throw new \LogicException('One or several user profile claims are missing');
        }

        return [$tokenClaims['sub'], $tokenClaims['email'], $tokenClaims['firstname'], $tokenClaims['lastname']];
    }
}
