from app.dependencies import get_config
from app.usecase.activate import activate_usecase
import secrets, urllib.parse

oauth_scopes = [
    'read_channel_localization',
    'read_channel_settings',
    'openid',
    'email',
    'profile'
];

def activate_usecase_with_openid(request, session):
    return activate_usecase(request,session, oauth_scopes)
    