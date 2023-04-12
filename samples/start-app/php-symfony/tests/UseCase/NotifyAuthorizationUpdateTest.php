<?php

namespace App\Tests\UseCase;

use App\Client\ClientFactory;
use App\Client\ClientFactoryInterface;
use App\Tests\MockApiTrait;
use App\Tests\Mocks\ChannelMock;
use App\Tests\Mocks\NotifyAuthorizationUpdateMock;
use App\UseCase\AppActivation;
use App\UseCase\FirstApiCall;
use App\UseCase\NotifyAuthorizationUpdate;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class NotifyAuthorizationUpdateTest extends TestCase
{
    use MockApiTrait;

    private NotifyAuthorizationUpdate $notifyAuthorizationUpdate;

    protected function setUp(): void
    {
        $clientFactory = $this->createMock(ClientFactoryInterface::class);
        $clientFactory->expects($this->once())
            ->method('create')
            ->willReturn(new Client(['handler' => $this->mockApi()]));
        $this->notifyAuthorizationUpdate = new NotifyAuthorizationUpdate(
            $clientFactory
        );
    }

    /**
     * @test
     *
     * execute() valid case
     *
     * @return void
     */
    public function testExecute(): void
    {
        $result = $this->notifyAuthorizationUpdate->execute(AppActivation::OAUTH_SCOPES);

        $this->assertEquals(NotifyAuthorizationUpdateMock::$response, json_decode($result->getBody()->getContents(), true));
    }

    /**
     * @test
     *
     * execute() error case
     *
     * @return void
     */
    public function testExecuteReturnForbidden(): void
    {
        $result = $this->notifyAuthorizationUpdate->execute([]);

        $this->assertEquals(NotifyAuthorizationUpdateMock::$badResponse, json_decode($result->getBody()->getContents(), true));
    }

}
