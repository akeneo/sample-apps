from sqlalchemy import Column, Integer, String
from .database import Base


class Token(Base):
    __tablename__ = "token"

    id = Column(Integer, primary_key=True, index=True)
    access_token = Column(String, unique=True, index=True)
