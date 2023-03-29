from app.usecase.activate import activate_usecase, oauth_scopes

oauth_scopes_openid = oauth_scopes + [
    'openid',
    'email',
    'profile'
];

def activate_usecase_with_openid(request, session):
    return activate_usecase(request,session, oauth_scopes_openid)
    