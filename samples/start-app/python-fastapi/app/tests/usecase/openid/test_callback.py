import unittest
from unittest.mock import patch, MagicMock
from app.usecase.openid.callback import callback_usecase_with_openid, fetch_openid_public_key, extract_claims_from_signed_token
from app.dependencies import build_user_agent
from app.model import schemas
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
    @patch('app.usecase.openid.callback.callback_usecase')
    def test_callback_usecase_with_openid(self, mock_callback_usecase, mock_fetch_openid_public_key, mock_extract_claims, mock_create_user):
        mock_callback_usecase.return_value.json.return_value = {'id_token': 'test_token'}
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

    @patch('jwt.decode')
    @patch('jwt.get_unverified_header')
    def test_extract_claims_from_signed_token(self, mock_get_unverified_header, mock_decode):
        # Setup
        id_token = 'eyJhbGciOiJSUzI1NiIsImFscGhhIjp0cnVlfQ.eyJpc3MiOiJEaW5vQ2hpZXNhLmdpdGh1Yi5pbyIsInN1YiI6InNoZW5pcXVhIiwiYXVkIjoiYW5uYSIsImlhdCI6MTY4MDAxMzc0OSwiZXhwIjoxNjgwMDE0MzQ5LCJwcm9wWCI6dHJ1ZX0.gbk8edWfu3hwVuvULTVAovqVnqaxm9EgJACOwdwOXuZAlwlpzsf8NeQkBcTlUzA5u5B1oRZThbx368xqcBE8O2an8J-rMKYjRKASbPWXJwE9KAh2BRMGsFLBDibpgmOo_LY_IOOvlmAMTxLR3GtgG8cM2kiWr0MoEdI1VXbgBOdj2sQ06Jz2zzs1rNsvBGV3BZw0lv_RAUAVdiSGAIPq0MqPK_47_FCM1fvHifphqmJvbhGGvx4apRLCmFdHLJyeddNYWKtaSW5PyfJIAgpJS01YU4_H71DV56useZu_KWVW73xJX0GV_LXotXm_2bADp7hL0ouYYIIjVonBhqYp5Q'
        
        signature = 'test_signature'
        issuer = 'DinoChiesa.github.io'
        mock_get_unverified_header.return_value={'alg': 'RS256'}
        mock_decode.return_value={
            "iss": "DinoChiesa.github.io",
            "sub": "sheniqua",
            "aud": "anna",
            "iat": 1680013775,
            "exp": 1680014375
        }

        # Exercise
        result = extract_claims_from_signed_token(id_token, signature, issuer)

        # Verify
        self.assertIsNotNone(result)
        self.assertIsInstance(result, dict)
        self.assertEqual(result['iss'], 'DinoChiesa.github.io')
        self.assertEqual(result['sub'], 'sheniqua')
        self.assertEqual(result['aud'], 'anna')