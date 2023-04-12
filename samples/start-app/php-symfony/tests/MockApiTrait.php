<?php

namespace App\Tests;

use App\Tests\Mocks\ChannelMock;
use App\Tests\Mocks\NotifyAuthorizationUpdateMock;
use App\Tests\Mocks\Oauth2Mock;
use App\Tests\Mocks\OpenIdPublicKeyMock;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

trait MockApiTrait
{
    public function mockApi(): callable
    {
        return function (Request $request) {
            [$statusCode, $content] = $this->getStubFromRequest($request);
            return new Response($statusCode, [], $content);
        };
    }

    function getStubFromRequest(Request $request): ?array
    {
        $uri = !empty($request->getUri()->getQuery())
            ? $request->getUri()->getPath() . '?' . $request->getUri()->getQuery()
            : $request->getUri()->getPath();

        return match (urldecode($uri)) {
            ChannelMock::API_URL => [200, json_encode(ChannelMock::$response),],
            Oauth2Mock::API_URL => [200, json_encode(Oauth2Mock::$response),],
            OpenIdPublicKeyMock::API_URL => [200, json_encode(OpenIdPublicKeyMock::$response),],
            NotifyAuthorizationUpdateMock::getApiUrl() => [200, json_encode(NotifyAuthorizationUpdateMock::$response),],
            NotifyAuthorizationUpdateMock::getBadUrl() => [403, json_encode(NotifyAuthorizationUpdateMock::$badResponse),],
            default => throw new \LogicException(),
        };
    }
}
