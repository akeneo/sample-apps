from fastapi import APIRouter, Request, Depends, HTTPException
from fastapi.responses import RedirectResponse
from sqlalchemy.orm import Session
from app.dependencies import get_db
from app.usecase.notifyAuthorizationUpdate import notify_authorization_update_usecase
from app.usecase.activate import oauth_scopes


router = APIRouter()

@router.get("/notify-authorization-update")
def notify_authorization_update(request: Request, db: Session = Depends(get_db)):

    response = notify_authorization_update_usecase(db, oauth_scopes)

    if response.status_code != 200:
        raise HTTPException(status_code=response.status_code, detail=response.json())


    return RedirectResponse('/')
