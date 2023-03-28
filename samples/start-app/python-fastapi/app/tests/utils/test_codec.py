import unittest
from Crypto.Cipher import AES
import binascii
from app.utils.codec import encoder, decoder

class TestCodec(unittest.TestCase):

    def test_encoder_decoder(self):
        key = 'abcdefghijklmnopqrstuvwxyz012345'
        message = "This is a secret message"
        encrypted = encoder(message, key)
        decrypted = decoder(encrypted['data'], key, encrypted['iv'])
        self.assertEqual(message, decrypted)

    def test_invalid_key(self):
        key = 'short' # invalid key length
        message = "This is a secret message"
        with self.assertRaises(ValueError):
            encrypted = encoder(message, key)

    def test_invalid_iv(self):
        key = 'abcdefghijklmnopqrstuvwxyz012345'
        message = "This is a secret message"
        encrypted = encoder(message, key)
        encrypted['iv'] = 'invalid'  # invalid iv format
        with self.assertRaises(binascii.Error):
            decrypted = decoder(encrypted['data'], key, encrypted['iv'])