from fastapi import FastAPI, Depends
from .persistence.database import engine
from .controller import activate, callback, firstApiCall
from .persistence import models
from .dependencies import get_session

app = FastAPI()

models.Base.metadata.create_all(bind=engine)

app.include_router(activate.router, dependencies=[Depends(get_session)])
app.include_router(callback.router, dependencies=[Depends(get_session)])
app.include_router(firstApiCall.router)

