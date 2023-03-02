<?php

namespace Client;

use App\Client\ClientFactory;
use App\Repository\TokenRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{

    private MockObject $tokenRepository;

    protected function setUp(): void
    {
        $this->tokenRepository = $this->createMock(TokenRepository::class);
    }


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

        print_r($client, 1);
    }

}
