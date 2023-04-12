from app.persistence import tokenRepository
from app.dependencies import get_config, build_user_agent
import urllib.parse
from urllib.parse import urljoin

import requests

def notify_authorization_update_usecase(db, oauth_scopes):    
    api_url = urljoin(get_config('AKENEO_PIM_URL'), '/connect/apps/v1/scopes/update?scopes=')
    api_url = api_url + urllib.parse.quote(' '.join(oauth_scopes))
    token = tokenRepository.get_token(db)

    response = requests.post(api_url,  headers={
        'Authorization': 'Bearer %s' % token.access_token,
        'User-Agent':  build_user_agent()
    })

    return response
