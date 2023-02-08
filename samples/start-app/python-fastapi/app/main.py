from fastapi import FastAPI, Depends
from .persistence.database import engine
from .controller import activate, callback, firstApiCall, homepage
from .persistence import models
from .dependencies import get_session
import logging

app = FastAPI()

models.Base.metadata.create_all(bind=engine)

app.include_router(activate.router, dependencies=[Depends(get_session)])
app.include_router(callback.router, dependencies=[Depends(get_session)])
app.include_router(firstApiCall.router)
app.include_router(homepage.router)

logging.basicConfig(filename='var/app.log', filemode='w', format='%(name)s - %(levelname)s - %(message)s')
