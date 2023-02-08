from dotenv import dotenv_values
from .persistence.database import SessionLocal
import requests

session = requests.Session()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

def get_config(name):
    return dotenv_values('.env.local')[name]
    
def get_session():
    return session;
