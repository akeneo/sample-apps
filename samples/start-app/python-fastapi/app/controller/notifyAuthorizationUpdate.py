from fastapi import APIRouter, Request, Depends
from fastapi.responses import RedirectResponse
from sqlalchemy.orm import Session
from app.dependencies import get_db
from app.usecase.notifyAuthorizationUpdate import notify_authorization_update_usecase


router = APIRouter()

@router.get("/notify-authorization-update")
def notify_authorization_update(request: Request, db: Session = Depends(get_db)):

    response = notify_authorization_update_usecase(db)

    return RedirectResponse('/')
