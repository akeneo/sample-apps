from fastapi import FastAPI
from sqlalchemy.orm import Session
from .database import engine
from .controller import activate, callback, firstApiCall
import secrets, requests, urllib.parse, hashlib

app = FastAPI()

models.Base.metadata.create_all(bind=engine)

app.include_router(activate.router)
app.include_router(callback.router)
app.include_router(first_api_call.router)

