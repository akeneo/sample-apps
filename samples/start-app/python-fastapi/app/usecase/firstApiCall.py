from app.persistence import tokenRepository
from app.dependencies import get_config, build_user_agent
from urllib.parse import urljoin

import requests

def first_api_call_usecase(db):

    # Replace by the API endpoint you want to call. Here an example with channels
    # https://api.akeneo.com/api-reference.html#get_channels
    api_url = urljoin(get_config('AKENEO_PIM_URL'), '/api/rest/v1/channels')
    token = tokenRepository.get_token(db)

    response = requests.get(api_url,  headers={
        'Authorization': 'Bearer %s' % token.access_token,
        'User-Agent':  build_user_agent()
    })
    
    return response
