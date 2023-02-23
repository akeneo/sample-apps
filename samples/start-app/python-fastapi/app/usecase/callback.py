from ..persistence import tokenRepository
from ..model import schemas
from ..dependencies import get_config
import secrets, requests, hashlib

get_app_token_url = '/connect/apps/v1/oauth2/token'

def callback_usecase(request, db, session):
    session_state = session.headers['oauth2_state']
    state = request.query_params['state'] or ''
    if state == '' or state != session_state:
        exit('Invalide state')

    authorization_code = request.query_params['code'] or ''
    if authorization_code == '':
        exit('Missing authorization code')

    pim_url = session.headers['pim_url']
    if pim_url == '':
        exit('No PIM url in session')

    code_identifier = secrets.token_hex(30)
    code_challenge = hashlib.sha256((code_identifier + get_config('CLIENT_SECRET')).encode('utf-8')).hexdigest()

    access_token_url = pim_url+'%s' % get_app_token_url

    access_token_request_payload = {
        'client_id': get_config('CLIENT_ID'),
        'code_identifier': code_identifier,
        'code_challenge': code_challenge,
        'code': authorization_code,
        'grant_type': 'authorization_code'
    }

    user_agent = 'AkeneoSampleApp/python-fastapi'
    user_agent = user_agent + ' Version/' + get_config('APPLICATION_VERSION') if get_config(
        'APPLICATION_VERSION') else user_agent + ''
    user_agent = user_agent + ' Docker/' + get_config('DOCKER_VERSION') if get_config(
        'DOCKER_VERSION') else user_agent + ''

    access_token_request_headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'User-Agent':  user_agent
    }

    response = requests.post(access_token_url, data=access_token_request_payload, headers=access_token_request_headers)

    token = schemas.TokenCreate(access_token=response.json()['access_token'])

    tokenRepository.create_token(db=db, token=token)
    
    return response
