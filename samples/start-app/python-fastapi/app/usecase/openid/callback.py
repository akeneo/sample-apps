from app.persistence import userRepository
from app.model import schemas
from app.dependencies import get_config, build_user_agent
from app.usecase.callback import callback_usecase
from app.utils.codec import encoder
from urllib.parse import urljoin
from cryptography.x509 import load_pem_x509_certificate
from jwt import decode
from cryptography.hazmat.backends import default_backend
import requests


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
    
    cert = load_pem_x509_certificate(signature.encode('utf-8'), default_backend())

    jwt_payload =  decode(id_token, key=cert.public_key(), algorithms=['RS256'], options={"verify_signature": True, "verify_iat": False, "verify_aud": False})

    # Verify the issuer
    if jwt_payload.get('iss') != issuer:
        raise ValueError('Invalid issuer')

    return jwt_payload
