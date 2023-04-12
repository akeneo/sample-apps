<?php
declare(strict_types=1);

namespace App\Tests\Mocks;

use App\UseCase\AppActivation;
use App\UseCase\NotifyAuthorizationUpdate;

final class NotifyAuthorizationUpdateMock
{
    public static string $response = 'ok';
    public static array $badResponse = ["code" => "403", "message" => "Forbidden"];

    public static function getApiUrl(): string
    {
        return NotifyAuthorizationUpdate::GET_APP_SCOPES_UPDATE . implode(' ', AppActivation::OAUTH_SCOPES);
    }

    public static function getBadUrl(): string
    {
        return NotifyAuthorizationUpdate::GET_APP_SCOPES_UPDATE;
    }
}
