from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session
from ..dependencies import get_db
from ..usecase.firstApiCall import first_api_call_usecase


router = APIRouter()

@router.get("/first-api-call")
def first_api_call(request: Request, db: Session = Depends(get_db)):

    response = first_api_call_usecase(db)

    return response.json()
