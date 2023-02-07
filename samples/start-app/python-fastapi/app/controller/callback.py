from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session
from ..dependencies import get_db, get_session
from ..usecase.callback import callback_usecase


router = APIRouter()

@router.get("/callback")
def callback(request: Request, db: Session = Depends(get_db), session: object = Depends(get_session)):
    response = callback_usecase(request, db, session)

    return response.json()