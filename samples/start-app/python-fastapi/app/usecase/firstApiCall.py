from ..persistence import tokenRepository
from ..dependencies import get_config
import requests

def first_api_call_usecase(db):

    # Replace by the API endpoint you want to call. Here an example with channels
    # https://api.akeneo.com/api-reference.html#get_channels
    apiUrl = get_config('AKENEO_PIM_URL') + '/api/rest/v1/channels'
    token = tokenRepository.get_token(db)
    response = requests.get(apiUrl,  headers={
        'Authorization': 'Bearer %s' % token.access_token,
        'X-APP-SOURCE':  'startApp-python',
    })
    
    return response
