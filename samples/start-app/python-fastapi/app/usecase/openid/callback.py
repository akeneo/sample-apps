from app.persistence import userRepository
from app.model import schemas
from app.dependencies import get_config, build_user_agent
from app.usecase.callback import callback_usecase
from app.utils.codec import encoder, decoder
from urllib.parse import urljoin
import requests
import jwt
from jwt import decode, get_unverified_header
from jwt.algorithms import get_default_algorithms


openid_public_key = '/connect/apps/v1/openid/public-key'

def callback_usecase_with_openid(request, db, session):
    response = callback_usecase(request, db, session)

    pim_url = session.headers['pim_url']
    if pim_url == '':
        raise ValueError('No PIM url in session')
    
    content = response.json()
    openid_public_key = fetch_openid_public_key(pim_url)
    claims = extract_claims_from_signed_token(content['id_token'], openid_public_key, pim_url)

    user = schemas.UserCreate(
        email = claims['email'],
        firstname = claims['firstname'],
        lastname = claims['lastname'],
        sub = claims['sub']
    )

    userRepository.create_user(db=db, user=user)
    
    return encoder(user.sub, get_config('SUB_HASH_KEY'))


def fetch_openid_public_key(pim_url):

    openid_public_key_url = urljoin(pim_url, openid_public_key)

    response = requests.get(openid_public_key_url,  headers={
        'User-Agent':  build_user_agent()
    })

    contents = response.json()
    if not 'public_key' in contents:
       raise Exception('Failed to retrieve openid public key')
    
    if not isinstance(contents['public_key'], str):
       raise Exception('OpenID public key is not a string')

    return contents['public_key']

def extract_claims_from_signed_token(id_token: str, signature: str, issuer: str):
    algorithm = 'RS256'  # assuming the algorithm used is RSA with SHA-256
    jwt_header = get_unverified_header(id_token)
    jwt_payload = decode(id_token, signature, algorithms=[algorithm], options={"verify_signature": False})

    # Verify the issuer
    if jwt_payload.get('iss') != issuer:
        raise ValueError('Invalid issuer')

    # Verify the signature
    # algorithms = get_default_algorithms()
    # signer = algorithms[algorithm]
    # print(signature)
    # key = signer.prepare_key(signature)
    # if not signer.verify(id_token.encode('utf-8'), key, jwt_header):
    #     raise ValueError('Invalid signature')

    return jwt_payload