<?php
declare(strict_types=1);

namespace App\Tests\Mocks;

use App\UseCase\NotifyAuthorizationUpdate;

final class NotifyAuthorizationUpdateMock
{
    const API_URL = NotifyAuthorizationUpdate::GET_APP_SCOPES_UPDATE . 'read_channel_localization read_channel_settings write_catalog_structure write_categories openid email profile';

    public static string $response = 'ok';
}
