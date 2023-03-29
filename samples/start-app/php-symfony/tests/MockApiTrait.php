<?php

namespace App\Tests;

use App\Tests\Mocks\ChannelMock;
use App\Tests\Mocks\Oauth2Mock;
use App\Tests\Mocks\OpenIdPublicKeyMock;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

trait MockApiTrait
{
    public function mockApi(): callable
    {
        return function (Request $request) {
            return new Response(200, [], $this->getStubFromRequest($request));
        };
    }

    function getStubFromRequest(Request $request): ?string
    {
        $uri = !empty($request->getUri()->getQuery())
            ? $request->getUri()->getPath() . '?' . $request->getUri()->getQuery()
            : $request->getUri()->getPath();

        return match (urldecode($uri)) {
            ChannelMock::API_URL => json_encode(ChannelMock::$response),
            Oauth2Mock::API_URL => json_encode(Oauth2Mock::$response),
            OpenIdPublicKeyMock::API_URL => json_encode(OpenIdPublicKeyMock::$response),
            default => throw new \LogicException(),
        };
    }
}
