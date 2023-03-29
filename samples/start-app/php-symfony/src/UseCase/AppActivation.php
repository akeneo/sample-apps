<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Entity\Exception\QueryParametersException;

final class AppActivation
{
    // Find the complete list of available authorization scopes at the link below
    // https://api.akeneo.com/apps/authentication-and-authorization.html#authorization-and-authentication-scopes
    const OAUTH_SCOPES = [
        'read_channel_localization',
        'read_channel_settings',
        'openid',
        'email',
        'profile'
    ];
    const GET_AUTHORIZATION_URL = '%s/connect/apps/v1/authorize?%s';

    public function __construct(private readonly string $oauthClientId)
    {
    }

    /**
     * @throws QueryParametersException
     */
    public function execute(&$session, $pimUrl): string
    {
        if (empty($pimUrl)) {
            throw new QueryParametersException(QueryParametersException::MISSING_PIM_URL);
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
