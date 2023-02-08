from sqlalchemy.orm import Session

from . import models
from ..model import schemas

def get_token(db: Session):
    return db.query(models.Token).order_by(models.Token.id.desc()).first()

def create_token(db: Session, token: schemas.TokenCreate):
    token_from_db = get_token(db);
    db_token = models.Token(access_token=token.access_token)
    if token_from_db is not None and token_from_db.access_token == token.access_token:
        return db_token

    db.add(db_token)
    db.commit()
    db.refresh(db_token)
    return db_token
