from sqlalchemy.orm import Session

from . import models, schemas

def get_token(db: Session):
    return db.query(models.Token).order_by(models.Token.id.desc()).first()

def create_token(db: Session, token: schemas.TokenCreate):
    db_token = models.Token(access_token=token.access_token)
    db.add(db_token)
    db.commit()
    db.refresh(db_token)
    return db_token
