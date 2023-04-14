from app.dependencies import get_config
import secrets, urllib.parse

oauth_scopes = [
    'read_channel_localization',
    'read_channel_settings',
]

get_authorization_url = '%s/connect/apps/v1/authorize?%s'

def activate_usecase(request, session, scopes=[]):
    if 'pim_url' not in request.query_params or request.query_params['pim_url'] == '':
        raise Exception('Missing PIM URL in the query')

    pim_url = request.query_params['pim_url']

    # create a random state for preventing cross-site request forgery
    state = secrets.token_hex(10)

    # Store in the token session the state and the PIM URL
    session.headers.update({'oauth2_state': state})
    session.headers.update({'pim_url': pim_url})

    authorize_url_params = urllib.parse.urlencode(
        {
            'response_type': 'code',
            'client_id': get_config('CLIENT_ID'),
            'scope': ' '.join(scopes + oauth_scopes),
            'state': state
        }
    )

    return  get_authorization_url % (pim_url, authorize_url_params)
