from dotenv import dotenv_values
from .database import SessionLocal

config = dotenv_values('.env')
session = requests.Session()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
