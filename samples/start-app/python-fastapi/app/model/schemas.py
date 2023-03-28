from pydantic import BaseModel


class TokenBase(BaseModel):
    access_token: str


class TokenCreate(TokenBase):
    pass


class Token(TokenBase):
    id: int

    class Config:
        orm_mode = True

class UserBase(BaseModel):
    email: str
    firstname: str
    lastname: str
    sub: str

class UserCreate(UserBase):
    pass


class User(UserBase):
    id: int

    class Config:
        orm_mode = True
