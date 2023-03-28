import unittest
from unittest.mock import patch, MagicMock
from app.usecase.openid.callback import callback_usecase_with_openid
from fastapi import Request
from sqlalchemy.orm import Session
import jwt


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
    @patch('app.usecase.callback.callback_usecase')
    def test_callback_usecase_with_openid(self, mock_callback_usecase, mock_fetch_openid_public_key, mock_extract_claims, mock_create_user):
        mock_callback_usecase.return_value = {'id_token': 'test_token'}
        mock_fetch_openid_public_key.return_value = 'test_public_key'
        mock_extract_claims.return_value = {
            'email': 'test@example.com',
            'firstname': 'John',
            'lastname': 'Doe',
            'sub': 'test_sub'
        }
        mock_create_user.return_value = None

        # Exercise
        result = callback_usecase_with_openid(self.request, self.db, self.session)

        # # Verify
        # self.assertIsNotNone(result)
        # self.assertIsInstance(result, str)

        # mock_callback_usecase.assert_called_once_with(self.request, self.db, self.session)
        # mock_fetch_openid_public_key.assert_called_once_with(self.session.headers['pim_url'])
        # mock_extract_claims.assert_called_once_with('test_token', 'test_public_key', session.headers['pim_url'])
        # mock_create_user.assert_called_once_with(db=self.db, user=schemas.UserCreate(
        #     email='test@example.com',
        #     firstname='John',
        #     lastname='Doe',
        #     sub='test_sub'
        # ))

    # @patch('requests.get')
    # def test_fetch_openid_public_key(self, mock_get):
    #     # Setup
    #     pim_url = 'https://example.com'
    #     mock_get.return_value = {'public_key': 'test_public_key'}

    #     # Exercise
    #     result = fetch_openid_public_key(pim_url)

    #     # Verify
    #     self.assertIsNotNone(result)
    #     self.assertIsInstance(result, str)
    #     self.assertEqual(result, 'test_public_key')

    #     mock_get.assert_called_once_with('https://example.com/connect/apps/v1/openid/public-key', headers={'User-Agent': build_user_agent()})

    # def test_extract_claims_from_signed_token(self, mock_get_unverified_header, mock_decode):
    #     # Setup
    #     id_token = 'test_token'
    #     signature = 'test_signature'
    #     issuer = 'test_issuer'
    #     mock_get_unverified_header.return_value={'alg': 'RS256'}
    #     mock_decode.return_value={
    #         'iss': 'test_issuer',
    #         'email': 'test@example.com',
    #         'firstname': 'John',
    #         'lastname': 'Doe',
    #         'sub': 'test_sub'
    #     }

    #     with patch.object(self.jwt, 'get_unverified_header', mock_get_unverified_header):
    #         with patch.object(self.jwt, 'decode', mock_decode):
    #             # Exercise
    #             result = extract_claims_from_signed_token(id_token, signature, issuer)

    #     # Verify
    #     self.assertIsNotNone(result)
    #     self.assertIsInstance(result, dict)
    #     self.assertEqual(result['iss'], 'test_issuer')
    #     self.assertEqual(result['email'], 'test@example.com')
    #     self.assertEqual(result['firstname'], 'John')
    #     self.assertEqual(result['lastname'], 'Doe')
    #     self.assertEqual(result['sub'], 'test_sub')