<?php

declare(strict_types=1);

namespace App\UseCase;

final class AppActivation
{
    const OAUTH_SCOPES = [
        'read_products',
        'write_products',
        'delete_products',
        'read_catalog_structure',
        'write_catalog_structure',
        'read_attribute_options',
        'write_attribute_options',
        'read_categories',
        'write_categories',
        'read_channel_localization',
        'read_channel_settings',
        'write_channel_settings',
        'read_association_types',
        'write_association_types',
        'read_catalogs',
        'write_catalogs',
        'delete_catalogs',
        'read_asset_families',
        'write_asset_families',
        'read_assets',
        'write_assets',
        'delete_assets',
        'read_reference_entities',
        'write_reference_entities',
        'read_reference_entity_records',
        'write_reference_entity_records',
    ];
    const GET_AUTHORIZATION_URL = '%s/connect/apps/v1/authorize?%s';

    public function __construct(private readonly string $oauthClientId)
    {
    }

    public function execute(&$session, $pimUrl): string
    {
        if (empty($pimUrl)) {
            exit('Missing PIM URL in the query');
        }

        // create a random state for preventing cross-site request forgery
        $state = bin2hex(random_bytes(10));

        // Store in the user session the state and the PIM URL
        $session['oauth2_state'] = $state;
        $session['pim_url'] = $pimUrl;

        // Build the parameters for the Authorization Request
        // https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
        $authorizeUrlParams = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->oauthClientId,
            'scope' => implode(' ', self::OAUTH_SCOPES),
            'state' => $state,
        ]);

        // Build the url for the Authorization Request using the PIM URL
        return sprintf(self::GET_AUTHORIZATION_URL, $pimUrl, $authorizeUrlParams);
    }
}
