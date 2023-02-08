from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session
from ..dependencies import get_db, get_session
from ..usecase.callback import callback_usecase
from fastapi.responses import HTMLResponse
from ..persistence import tokenRepository
from pathlib import Path



router = APIRouter()

@router.get("/callback")
def callback(request: Request, db: Session = Depends(get_db), session: object = Depends(get_session)):
    callback_usecase(request, db, session)

    token = tokenRepository.get_token(db)

    if(token is None):
            return HTMLResponse(content=Path('app/templates/no_access_token.html').read_text(), status_code=200)


    return HTMLResponse(content=Path('app/templates/access_token.html').read_text(), status_code=200)
