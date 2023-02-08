from fastapi import APIRouter, Request, Depends
from fastapi.responses import RedirectResponse
from ..dependencies import get_session
from ..usecase.activate import activate_usecase

router = APIRouter()

@router.get("/activate")
def activate(request: Request, session: object = Depends(get_session)):
    url = activate_usecase(request, session)

    return RedirectResponse(url)
