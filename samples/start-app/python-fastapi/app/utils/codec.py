import os
from Crypto.Cipher import AES
from Crypto.Random import get_random_bytes
import binascii

CYPHER = "AES-256-CTR"

def encoder(text, key):
    iv = get_random_bytes(16)
    cipher = AES.new(key.encode(), AES.MODE_CFB, iv=iv)
    data = cipher.encrypt(text.encode())
    return {'data':binascii.hexlify(data).decode(), 'iv': binascii.hexlify(iv).decode()}

def decoder(encrypted_data, key, encrypted_iv):
    iv = binascii.unhexlify(encrypted_iv.encode())
    data = binascii.unhexlify(encrypted_data.encode())
    cipher = AES.new(key.encode(), AES.MODE_CFB, iv=iv)

    return cipher.decrypt(data).decode()
