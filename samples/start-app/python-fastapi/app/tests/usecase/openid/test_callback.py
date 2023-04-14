import unittest
from unittest.mock import patch, MagicMock
from app.usecase.openid.callback import callback_usecase_with_openid, fetch_openid_public_key, extract_claims_from_signed_token
from app.dependencies import build_user_agent
from app.model import schemas
from fastapi import Request
from sqlalchemy.orm import Session
import jwt
from cryptography.hazmat.backends import default_backend


class TestCallbackUseCase(unittest.TestCase):

    def setUp(self):
        self.request = MagicMock(spec=Request)
        self.db = MagicMock(spec=Session)
        self.jwt = MagicMock(spec=jwt)
        self.session = MagicMock()
        self.session.headers = {'oauth2_state': 'state_value', 'pim_url': 'http://pim-url.com'}
        self.request.query_params = {'state': 'state_value', 'code': 'auth_code'}


    @patch('app.persistence.userRepository.create_user')
    @patch('app.usecase.openid.callback.extract_claims_from_signed_token')
    @patch('app.usecase.openid.callback.fetch_openid_public_key')
    @patch('app.usecase.openid.callback.callback_usecase')
    @patch('app.usecase.openid.callback.get_config')
    def test_callback_usecase_with_openid(self, mock_get_config, mock_callback_usecase, mock_fetch_openid_public_key, mock_extract_claims, mock_create_user):
        mock_callback_usecase.return_value.json.return_value = {'id_token': 'test_token'}
        mock_fetch_openid_public_key.return_value = 'test_public_key'
        mock_extract_claims.return_value = {
            'email': 'test@example.com',
            'firstname': 'John',
            'lastname': 'Doe',
            'sub': 'test_sub'
        }
        mock_create_user.return_value = None
        mock_get_config.return_value = '3UtNbWGUHsJXlD6e4vT5fucvYUe2P9S1'

        # Exercise
        result = callback_usecase_with_openid(self.request, self.db, self.session)

        # Verify
        self.assertIsNotNone(result)
        self.assertIsInstance(result, dict)

        mock_callback_usecase.assert_called_once_with(self.request, self.db, self.session)
        mock_fetch_openid_public_key.assert_called_once_with(self.session.headers['pim_url'])
        mock_extract_claims.assert_called_once_with('test_token', 'test_public_key', self.session.headers['pim_url'])
        mock_create_user.assert_called_once_with(db=self.db, user=schemas.UserCreate(
            email='test@example.com',
            firstname='John',
            lastname='Doe',
            sub='test_sub'
        ))

    @patch('requests.get')
    def test_fetch_openid_public_key(self, mock_get):
        # Setup
        pim_url = 'https://pim.com'
        mock_get.return_value.json.return_value = {'public_key': 'test_public_key'}

        # Exercise
        result = fetch_openid_public_key(pim_url)

        # Verify
        self.assertIsNotNone(result)
        self.assertIsInstance(result, str)
        self.assertEqual(result, 'test_public_key')

        mock_get.assert_called_once_with('https://pim.com/connect/apps/v1/openid/public-key', headers={'User-Agent': build_user_agent()})

    @patch('app.usecase.openid.callback.load_pem_x509_certificate')
    @patch('app.usecase.openid.callback.decode')
    def test_extract_claims_from_signed_token(self,mock_decode, mock_certificate):
        # Setup
        id_token = 'example_id_token'
        signature = 'example_signature'
        issuer = 'example_issuer'

        mock_certificate.return_value.public_key.return_value = 'example_public_key'
        mock_jwt_payload = {'iss': 'example_issuer', 'sub': 'example_subject'}
        mock_decode.return_value = mock_jwt_payload

        # Exercise
        result = extract_claims_from_signed_token(id_token, signature, issuer)

        # Verify
        self.assertEqual(result, mock_jwt_payload)
        mock_certificate.assert_called_once_with(signature.encode('utf-8'), default_backend())
        mock_decode.assert_called_once_with(id_token, key='example_public_key', algorithms=['RS256'], options={"verify_signature": True, "verify_iat": False, "verify_aud": False})

    @patch('app.usecase.openid.callback.load_pem_x509_certificate')
    @patch('app.usecase.openid.callback.decode')
    def test_extract_claims_from_signed_token_with_bad_issuer(self,mock_decode, mock_certificate):
        # Setup
        id_token = 'example_id_token'
        signature = 'example_signature'
        issuer = 'bad_issuer'

        mock_certificate.return_value.public_key.return_value = 'example_public_key'

        mock_jwt_payload = {'iss': 'example_issuer', 'sub': 'example_subject'}
        mock_decode.return_value = mock_jwt_payload

        # Verify
        with self.assertRaises(ValueError):
            result = extract_claims_from_signed_token(id_token, signature, issuer)