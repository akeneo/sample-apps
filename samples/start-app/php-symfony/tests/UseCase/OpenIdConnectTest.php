<?php

namespace App\Tests\UseCase;

use App\Entity\Exception\OpenIdConnectException;
use App\Entity\Exception\SessionInformationException;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\MockApiTrait;
use App\Tests\Mocks\OpenIdPublicKeyMock;
use App\UseCase\Codec;
use App\UseCase\OpenIdConnect;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Log\Logger;

class OpenIdConnectTest extends TestCase
{
    use MockApiTrait;

    private OpenIdConnect $openIdConnect;
    const PRIVATE_KEY = 'private_key';

    protected function setUp(): void
    {
        $client = new Client(['handler' => $this->mockApi()]);
        $userRepository = $this->createMock(UserRepository::class);
        $this->openIdConnect = new OpenIdConnect($client, $userRepository, self::PRIVATE_KEY);
    }

    /**
     * @test
     *
     * execute() with empty PIM URL
     *
     * @throws SessionInformationException
     * @throws GuzzleException
     */
    public function testExecuteWithEmptyPimUrl() : void
    {
        $this->expectException(SessionInformationException::class);
        $this->expectExceptionMessage(SessionInformationException::MISSING_PIM_URL);

        $session = array();
        $idToken = 'id_token';

        $this->openIdConnect->execute($session, $idToken);
    }

    /**
     * @test
     *
     * execute() with a missing claim in token
     *
     * @throws SessionInformationException
     * @throws GuzzleException
     */
    public function testExecuteWithMissingClaim() : void
    {

        $this->expectException(OpenIdConnectException::class);
        $this->expectExceptionMessage(OpenIdConnectException::MISSING_CLAIM);

        $pimUrl = 'http://a_random_pim_url.com';
        $firstname = 'John';
        $lastname = 'Doe';

        $token = $this->generateIdToken(
            $pimUrl,
            OpenIdPublicKeyMock::$response['private_key'],
            OpenIdPublicKeyMock::$response['public_key'],
            ['firstname' => $firstname, 'lastname' => $lastname]
        );

        $session = array('pim_url' => $pimUrl);

        $this->openIdConnect->execute($session, $token->toString());
    }

    /**
     * @test
     *
     * execute()
     *
     * @throws SessionInformationException
     * @throws GuzzleException
     */
    public function testExecute() : void
    {

        $pimUrl = 'http://a_random_pim_url.com';
        $email = 'john-doe@akeneo.com';
        $firstname = 'John';
        $lastname = 'Doe';

        $token = $this->generateIdToken(
            $pimUrl,
            OpenIdPublicKeyMock::$response['private_key'],
            OpenIdPublicKeyMock::$response['public_key'],
            ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email]
        );

        $session = array('pim_url' => $pimUrl);

        list($data, $iv) = $this->openIdConnect->execute($session, $token->toString());

        $decodedSub = Codec::decode($data, self::PRIVATE_KEY, $iv);

        $this->assertEquals($token->claims()->get("sub"), $decodedSub);
    }

    /**
     * @param array<string, string> $withClaims
     */
    private function generateIdToken(
        string $url,
        string $privateKey,
        string $publicKey,
        array $withClaims = ['firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john-doe@akeneo.com']
    ): Token {
        $jwtConfig = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey),
            InMemory::plainText($publicKey),
        );

        $now = new \DateTimeImmutable();

        $jwtTokenBuilder = $jwtConfig->builder()
            ->issuedBy($url)
            ->identifiedBy('uuid')
            ->relatedTo('ppid')
            ->permittedFor('clientId')
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'));

        foreach ($withClaims as $name => $value) {
            $jwtTokenBuilder = $jwtTokenBuilder->withClaim($name, $value);
        }

        return $jwtTokenBuilder->getToken(
            $jwtConfig->signer(),
            $jwtConfig->signingKey()
        );
    }
}
