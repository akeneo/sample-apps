<?php

namespace Client;

use App\Client\ClientFactory;
use App\Entity\Token;
use App\Repository\TokenRepository;
use GuzzleHttp\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{

    private MockObject $tokenRepository;

    protected function setUp(): void
    {
        $this->tokenRepository = $this->createMock(TokenRepository::class);
    }


    /**
     * @test
     *
     * create() but not token has been found
     *
     * @return void
     */
    public function testCreateNoToken(): void
    {
        $pimUrl = 'http://a_random_pim_url.com';
        $dockerVersion = '23.0.1';
        $applicationVersion = '1.0.0';
        $clientFactory = new ClientFactory($pimUrl, $this->tokenRepository, $dockerVersion, $applicationVersion);

        $this->tokenRepository->expects($this->once())
            ->method('hasToken')
            ->willReturn(false);

        $client = $clientFactory->create();

        $this->assertEquals(new Client(), $client);
    }

    /**
     * @test
     *
     * create() but a token has been found and no version information has been provided
     *
     * @deprecated because GuzzleHttp Client getConfig has been deprecated and no workaround is available
     * @return void
     */
    public function testCreateWithTokenWithoutVersionInformation(): void
    {
        $pimUrl = 'http://a_random_pim_url.com';
        $clientFactory = new ClientFactory($pimUrl, $this->tokenRepository, '', '');

        $token = Token::create('myAcessTokenWithoutDockerVersion');

        $expectedUserAgent = 'AkeneoSampleApp/php-symfony';
        $expectedAuthorization = sprintf("Bearer %s", $token->getAccessToken());

        $this->tokenRepository->expects($this->once())
            ->method('hasToken')
            ->willReturn(true);
        $this->tokenRepository->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $client = $clientFactory->create();

        $this->assertEquals($pimUrl, $client->getConfig('base_uri'));
        $this->assertEquals($expectedUserAgent, $client->getConfig('headers')["User-Agent"]);
        $this->assertEquals($expectedAuthorization, $client->getConfig('headers')["Authorization"]);
    }

    /**
     * @test
     *
     * create() but a token has been found and only application version information has been provided
     *
     * @deprecated because GuzzleHttp Client getConfig has been deprecated and no workaround is available
     * @return void
     */
    public function testCreateWithTokenWithoutDockerVersion(): void
    {
        $pimUrl = 'http://a_random_pim_url.com';
        $applicationVersion = '1.0.0';
        $clientFactory = new ClientFactory($pimUrl, $this->tokenRepository, '', $applicationVersion);

        $token = Token::create('myAcessTokenWithoutDockerVersion');

        $expectedUserAgent = 'AkeneoSampleApp/php-symfony Version/'
            . $applicationVersion;
        $expectedAuthorization = sprintf("Bearer %s", $token->getAccessToken());

        $this->tokenRepository->expects($this->once())
            ->method('hasToken')
            ->willReturn(true);
        $this->tokenRepository->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $client = $clientFactory->create();

        $this->assertEquals($pimUrl, $client->getConfig('base_uri'));
        $this->assertEquals($expectedUserAgent, $client->getConfig('headers')["User-Agent"]);
        $this->assertEquals($expectedAuthorization, $client->getConfig('headers')["Authorization"]);
    }

    /**
     * @test
     *
     * create() but a token has been found and all information have been provided
     *
     * @deprecated because GuzzleHttp Client getConfig has been deprecated and no workaround is available
     * @return void
     */
    public function testCreateWithTokenWithAllInformation(): void
    {
        $pimUrl = 'http://a_random_pim_url.com';
        $dockerVersion = '23.0.1';
        $applicationVersion = '1.0.0';
        $clientFactory = new ClientFactory($pimUrl, $this->tokenRepository, $dockerVersion, $applicationVersion);

        $token = Token::create('myAcessTokenWithAllInformation');

        $expectedUserAgent = 'AkeneoSampleApp/php-symfony Version/'
            . $applicationVersion
            . ' Docker/' . $dockerVersion;
        $expectedAuthorization = sprintf("Bearer %s", $token->getAccessToken());

        $this->tokenRepository->expects($this->once())
            ->method('hasToken')
            ->willReturn(true);
        $this->tokenRepository->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $client = $clientFactory->create();

        $this->assertEquals($pimUrl, $client->getConfig('base_uri'));
        $this->assertEquals($expectedUserAgent, $client->getConfig('headers')["User-Agent"]);
        $this->assertEquals($expectedAuthorization, $client->getConfig('headers')["Authorization"]);
    }

}
