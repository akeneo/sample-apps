<?php

namespace App\UseCase;

use App\Entity\Exception\SessionInformationException;
use App\Entity\User;
use App\Repository\UserRepository;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class OpenIdConnect
{
    const OPENID_PUBLIC_KEY = '/connect/apps/v1/openid/public-key';

    public function __construct(
        private readonly ClientInterface $client,
        private readonly UserRepository  $userRepository,
        private readonly string          $subHashKey)
    {
    }

    /**
     * @throws GuzzleException
     * @throws SessionInformationException
     */
    public function execute($session, $idToken): array
    {
        $pimUrl = $session['pim_url'] ?? '';
        if (empty($pimUrl)) {
            throw new SessionInformationException(SessionInformationException::MISSING_PIM_URL);
        }

        $openIdPublicKey = $this->fetchOpenIdPublicKey($pimUrl);
        $claims = $this->extractClaimsFromSignedToken($idToken, $openIdPublicKey, $pimUrl);
        list($sub, $email, $firstname, $lastname) = $this->getUserProfileFromTokenClaims($claims);

        $this->userRepository->upsert(User::create($email, $firstname, $lastname, $sub), true);

        return Codec::encode($sub, $this->subHashKey);
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
