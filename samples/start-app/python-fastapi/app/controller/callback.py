from fastapi import APIRouter, Request, Response, Depends,Cookie
from fastapi.responses import RedirectResponse
from sqlalchemy.orm import Session
from app.dependencies import get_db, get_session, openid_authentication, get_config
from app.usecase.callback import callback_usecase
from app.usecase.openid.callback import callback_usecase_with_openid
from app.persistence import tokenRepository, userRepository
from app.utils.codec import decoder
from fastapi.responses import HTMLResponse
from pathlib import Path
from typing import Optional

router = APIRouter()

@router.get("/callback")
def callback(request: Request,response: Response, db: Session = Depends(get_db), session: object = Depends(get_session)):
    
    token = tokenRepository.get_token(db)

    # You can remove the following condition if you're not using the OpenID protocol
    if openid_authentication():
        json = callback_usecase_with_openid(request, db, session)
        response = RedirectResponse("/access-oid")
        response.set_cookie(key="sub", value=json['data'])
        response.set_cookie(key="vector", value=json['iv'])

        return response;


    callback_usecase(request, db, session)
    if token is None:
        return HTMLResponse(content=Path('app/templates/no_access_token.html').read_text(), status_code=200)

    return HTMLResponse(content=Path('app/templates/access_token.html').read_text(), status_code=200)

# You can remove the following function if you're not using the OpenID protocol
@router.get("/access-oid")
def access_token_status(request: Request,response: Response, db: Session = Depends(get_db), session: object = Depends(get_session)):
    sub = request.cookies.get("sub")
    vector = request.cookies.get("vector")

    token = tokenRepository.get_token(db)

    user = None
    if sub != None and vector != None:
        sub_decoded = decoder(sub, get_config('SUB_HASH_KEY'), vector)
        user = userRepository.get_user_by_sub(db, sub_decoded)

    if token is None:
        return HTMLResponse(content=Path('app/templates/openid/no_access_token.html').read_text(), status_code=200)

    if user is None:
        return HTMLResponse(content=Path('app/templates/access_token.html').read_text(), status_code=200)

    return HTMLResponse(content=Path('app/templates/openid/access_token.html')
                        .read_text()
                        .replace('{{user}}', user.firstname + ' ' + user.lastname)
                        .replace('{{email}}', user.email)
                        , status_code=200)
                        