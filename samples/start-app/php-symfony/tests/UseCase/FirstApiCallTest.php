<?php

namespace App\Tests\UseCase;

use App\Client\ClientFactory;
use App\Client\ClientFactoryInterface;
use App\Tests\MockApiTrait;
use App\Tests\Mocks\ChannelMock;
use App\UseCase\FirstApiCall;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class FirstApiCallTest extends TestCase
{
    use MockApiTrait;

    private FirstApiCall $firstApiCall;

    protected function setUp(): void
    {
        $clientFactory = $this->createMock(ClientFactoryInterface::class);
        $clientFactory->expects($this->once())
            ->method('create')
            ->willReturn(new Client(['handler' => $this->mockApi()]));
        $this->firstApiCall = new FirstApiCall(
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
        $result = $this->firstApiCall->execute();

        $this->assertEquals(ChannelMock::$response['code'], $result['code']);
        $this->assertEquals(ChannelMock::$response['currencies'], $result['currencies']);
        $this->assertEquals(ChannelMock::$response['locales'], $result['locales']);
        $this->assertEquals(ChannelMock::$response['category_tree'], $result['category_tree']);
        $this->assertEquals(ChannelMock::$response['labels'], $result['labels']);
    }

}
