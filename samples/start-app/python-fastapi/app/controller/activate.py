from fastapi import APIRouter, Request, Depends
from fastapi.responses import RedirectResponse
from app.dependencies import get_session, openid_authentication
from app.usecase.activate import activate_usecase

router = APIRouter()

@router.get("/activate")
def activate(request: Request, session: object = Depends(get_session)):

    # You can remove the following condition if you're not using the OpenID protocol
    if openid_authentication():
        oauth_scopes_openid = ['openid','email','profile']
        return RedirectResponse(activate_usecase(request, session, oauth_scopes_openid))

    return RedirectResponse(activate_usecase(request, session))
