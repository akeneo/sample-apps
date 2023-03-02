<?php
declare(strict_types=1);

namespace App\Tests\Mocks;

final class ChannelMock
{
    const API_URL = '/api/rest/v1/channels';

    public static array $response = [
        'code' => 'ecommerce',
        'currencies' =>
            [
                'USD',
                'EUR',
            ],
        'locales' =>
            [
                'de_DE',
                'en_US',
                'fr_FR',
            ],
        'category_tree' => 'master',
        'conversion_units' => [],
        'labels' =>
            [
                'en_US' => 'Ecommerce',
                'de_DE' => 'Ecommerce',
                'fr_FR' => 'Ecommerce',
            ],
    ];
}
