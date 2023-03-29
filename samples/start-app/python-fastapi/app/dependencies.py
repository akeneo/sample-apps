from dotenv import dotenv_values
from .persistence.database import SessionLocal
import requests

session = requests.Session()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

def get_config(name):
    return dotenv_values('.env.local')[name] if name in dotenv_values('.env.local') else False
    
def get_session():
    return session

def openid_authentication():
    return get_config('OPENID_AUTHENTICATION') == 'true' or get_config('OPENID_AUTHENTICATION')  == '1' or get_config('OPENID_AUTHENTICATION') == True

def build_user_agent():
    user_agent = 'AkeneoSampleApp/python-fastapi'
    user_agent = user_agent + ' Version/' + get_config('APPLICATION_VERSION') if get_config('APPLICATION_VERSION') else user_agent + ''
    return user_agent + ' Docker/' + get_config('DOCKER_VERSION') if get_config('DOCKER_VERSION') else user_agent + ''

def create_requests():
    s = requests.Session()
    s.headers.update({'User-Agent': build_user_agent()})
    return s
