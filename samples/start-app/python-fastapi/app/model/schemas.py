from pydantic import BaseModel


class TokenBase(BaseModel):
    access_token: str


class TokenCreate(TokenBase):
    pass


class Token(TokenBase):
    id: int

    class Config:
        orm_mode = True
