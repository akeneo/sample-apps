from sqlalchemy.orm import Session

from . import models
from app.model import schemas

def get_user(db: Session):
    return db.query(models.User).order_by(models.User.id.desc()).first()

def get_user_by_sub(db: Session, sub):
    return db.query(models.User).filter(models.User.sub == sub).first()

def create_user(db: Session, user: schemas.UserCreate):
    user_from_db = get_user(db);
    db_user = models.User(
        email = user.email,
        firstname = user.firstname,
        lastname = user.lastname,
        sub = user.sub
    )
    if user_from_db is not None and user_from_db.sub == user.sub:
        return db_user

    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user
