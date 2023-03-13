from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session
from app.dependencies import get_db, get_session
from app.persistence import tokenRepository
from fastapi.responses import HTMLResponse
from pathlib import Path



router = APIRouter()

@router.get("/")
def homepage(request: Request, db: Session = Depends(get_db), session: object = Depends(get_session)):

    token = tokenRepository.get_token(db)

    if(token is None):
            return HTMLResponse(content=Path('app/templates/no_access_token.html').read_text(), status_code=200)


    return HTMLResponse(content=Path('app/templates/access_token.html').read_text(), status_code=200)
