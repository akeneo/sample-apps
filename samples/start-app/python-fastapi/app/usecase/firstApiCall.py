from ..persistence import tokenRepository
from ..dependencies import get_config
from urllib.parse import urljoin

import requests

def first_api_call_usecase(db):

    # Replace by the API endpoint you want to call. Here an example with channels
    # https://api.akeneo.com/api-reference.html#get_channels
    api_url = urljoin(get_config('AKENEO_PIM_URL'), '/api/rest/v1/channels')
    token = tokenRepository.get_token(db)

    user_agent = 'AkeneoSampleApp/python-fastapi'
    user_agent = user_agent + ' Version/' + get_config('APPLICATION_VERSION') if get_config('APPLICATION_VERSION') else user_agent + ''
    user_agent = user_agent + ' Docker/' + get_config('DOCKER_VERSION') if get_config('DOCKER_VERSION') else user_agent + ''

    response = requests.get(api_url,  headers={
        'Authorization': 'Bearer %s' % token.access_token,
        'User-Agent':  user_agent
    })
    
    return response
