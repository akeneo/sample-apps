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

    def test_extract_claims_from_signed_token(self):
        # Setup
        id_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwczovL21hcnN1cy5zYW5kYm94LmNsb3VkLmFrZW5lby5jb20iLCJqdGkiOiJmZDAyNDA0OS01NGQ3LTQ2NDgtYTYxZS0zNmFkMGMyNjBmN2MiLCJzdWIiOiJhOGJiMDEyMy1lNjhkLTQzN2UtYjU5My0wZmUxN2MxYmQ1MzgiLCJhdWQiOiJiYWFkZTQ0MC05MjI3LTQzYWMtYjg1MC1kNTZkNzM0OGIzNGUiLCJpYXQiOjE2ODEyMDI2OTMuODQxMjE3LCJleHAiOjE2ODEyMDYyOTMuODQxMjE3LCJmaXJzdG5hbWUiOiJTYW11ZWwiLCJsYXN0bmFtZSI6IkdvbWlzIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJlbWFpbCI6InNhbXVlbC5nb21pc0BnZXRha2VuZW8uY29tIn0.oTblZvdyh8RV6gcnP3CCE79ucK8A8gFBDFIjo1H3rl_DQfmw6nVTMV682ihnN-WusMPw7dJZWnnid5et1Wq2EPxp1mtwOzGA_F15hMs_Ie_IFGjQnwVTPoJRH59HADbe30-ZwjloCxOfRPFhrK5d7W38CkSPaxlf0wdU7ZqPAMEW5oFFNsC-5NT1WRvHHPSvNTt1vsc5ZmAKH-FIksMiB2IwGQtGzYvrH1UTptZxrDk3r3cwVYNoVWndcgeUSQ0djXwzsgfzZwfifpnrLEw1UyBsjZhjKAPWQoTf0ddkk3VaGgU53axLHyD8F22sx_ycxV3UQx6Tv5buMzZj_MbqsQ'
        
        signature = '-----BEGIN CERTIFICATE-----\r\nMIIC7zCCAdegAwIBAgIUXaYsmjjYO0r4B3bW5ULJLCNRoVgwDQYJKoZIhvcNAQEL\r\nBQAwETEPMA0GA1UECgwGQWtlbmVvMB4XDTIzMDEyOTIxMDQxOFoXDTI0MDEyOTIx\r\nMDQxOFowETEPMA0GA1UECgwGQWtlbmVvMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A\r\nMIIBCgKCAQEA2tk0QrRYRPsMSetxiMwleifpC9BMq3rmsNIsFl6f9xYqG/TU/1NW\r\nnYvIzu/Ad9552eJa6k1qoU4d17ulWkZnGJxBoQhOtVSnXVAavyE6IhkC0fJ01Pd9\r\nWlFafcudHHbtgDERQQbSbWrWq5B1UQYpOqTvGUOmjV4i7/hZDFBWY2jsWFAKxLm+\r\nnbpV59yT4n85hA5Kt/swrh5dnYf/nXhAqxjE0sf5aFEA5C/KMlNYErVa821taeKL\r\nEy0/uEWtkxHU3i0/dqB9mL0OC1etkHdMU7uLVaadlFYVcFDDYkMu0Ix5Xb6j3Q9B\r\nF/22nZlesCUqtLEhR7oy0/gPJt8uUHwhqwIDAQABoz8wPTALBgNVHQ8EBAMCAQYw\r\nDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQURGKmWPRSE/Kf1rs0+dBkC6aV6LUw\r\nDQYJKoZIhvcNAQELBQADggEBAIYx0Bz+MiCjSTk/wp1KsL0SVtv1GupiRv3BqYhG\r\nlEIeVo/fmBQOuBmt1fbje6+OD2VaM0KJ4VTt/UrkyzE+VpmavrFynh/oWmkoospN\r\nU+rNfVP2JwombcYLQAyqurR5VAKea/uatnxwqL7TUMG+v9G1ImeBM4hRUWUD0Nce\r\nWybalg6AZeGGjZ0uaSM2v5IKG+8MoZaXuoiPzWJ6teo2CCEkoFBJZlLXOK6X9OSv\r\nO9yFEd9PVU4BrbAxHf9q9HHrh6SMKcdWTfpjL6LbydVIZYXdr2JC/aRF5hO9lxTj\r\nc5PKdB9GCPt3S0ad0n2jdRJy0j1Q2rMFaTJEWoR7SPeFxas=\r\n-----END CERTIFICATE-----'
        issuer = 'https://marsus.sandbox.cloud.akeneo.com'

        # Exercise
        result = extract_claims_from_signed_token(id_token, signature, issuer)

        # Verify
        self.assertIsNotNone(result)
        self.assertIsInstance(result, dict)
        self.assertEqual(result['iss'], issuer)
        self.assertEqual(result['sub'], 'a8bb0123-e68d-437e-b593-0fe17c1bd538')

    
    def test_extract_claims_from_signed_token_with_bad_issuer_raise_exception(self):
        # Setup
        id_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwczovL21hcnN1cy5zYW5kYm94LmNsb3VkLmFrZW5lby5jb20iLCJqdGkiOiJmZDAyNDA0OS01NGQ3LTQ2NDgtYTYxZS0zNmFkMGMyNjBmN2MiLCJzdWIiOiJhOGJiMDEyMy1lNjhkLTQzN2UtYjU5My0wZmUxN2MxYmQ1MzgiLCJhdWQiOiJiYWFkZTQ0MC05MjI3LTQzYWMtYjg1MC1kNTZkNzM0OGIzNGUiLCJpYXQiOjE2ODEyMDI2OTMuODQxMjE3LCJleHAiOjE2ODEyMDYyOTMuODQxMjE3LCJmaXJzdG5hbWUiOiJTYW11ZWwiLCJsYXN0bmFtZSI6IkdvbWlzIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJlbWFpbCI6InNhbXVlbC5nb21pc0BnZXRha2VuZW8uY29tIn0.oTblZvdyh8RV6gcnP3CCE79ucK8A8gFBDFIjo1H3rl_DQfmw6nVTMV682ihnN-WusMPw7dJZWnnid5et1Wq2EPxp1mtwOzGA_F15hMs_Ie_IFGjQnwVTPoJRH59HADbe30-ZwjloCxOfRPFhrK5d7W38CkSPaxlf0wdU7ZqPAMEW5oFFNsC-5NT1WRvHHPSvNTt1vsc5ZmAKH-FIksMiB2IwGQtGzYvrH1UTptZxrDk3r3cwVYNoVWndcgeUSQ0djXwzsgfzZwfifpnrLEw1UyBsjZhjKAPWQoTf0ddkk3VaGgU53axLHyD8F22sx_ycxV3UQx6Tv5buMzZj_MbqsQ'
        
        signature = '-----BEGIN CERTIFICATE-----\r\nMIIC7zCCAdegAwIBAgIUXaYsmjjYO0r4B3bW5ULJLCNRoVgwDQYJKoZIhvcNAQEL\r\nBQAwETEPMA0GA1UECgwGQWtlbmVvMB4XDTIzMDEyOTIxMDQxOFoXDTI0MDEyOTIx\r\nMDQxOFowETEPMA0GA1UECgwGQWtlbmVvMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A\r\nMIIBCgKCAQEA2tk0QrRYRPsMSetxiMwleifpC9BMq3rmsNIsFl6f9xYqG/TU/1NW\r\nnYvIzu/Ad9552eJa6k1qoU4d17ulWkZnGJxBoQhOtVSnXVAavyE6IhkC0fJ01Pd9\r\nWlFafcudHHbtgDERQQbSbWrWq5B1UQYpOqTvGUOmjV4i7/hZDFBWY2jsWFAKxLm+\r\nnbpV59yT4n85hA5Kt/swrh5dnYf/nXhAqxjE0sf5aFEA5C/KMlNYErVa821taeKL\r\nEy0/uEWtkxHU3i0/dqB9mL0OC1etkHdMU7uLVaadlFYVcFDDYkMu0Ix5Xb6j3Q9B\r\nF/22nZlesCUqtLEhR7oy0/gPJt8uUHwhqwIDAQABoz8wPTALBgNVHQ8EBAMCAQYw\r\nDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQURGKmWPRSE/Kf1rs0+dBkC6aV6LUw\r\nDQYJKoZIhvcNAQELBQADggEBAIYx0Bz+MiCjSTk/wp1KsL0SVtv1GupiRv3BqYhG\r\nlEIeVo/fmBQOuBmt1fbje6+OD2VaM0KJ4VTt/UrkyzE+VpmavrFynh/oWmkoospN\r\nU+rNfVP2JwombcYLQAyqurR5VAKea/uatnxwqL7TUMG+v9G1ImeBM4hRUWUD0Nce\r\nWybalg6AZeGGjZ0uaSM2v5IKG+8MoZaXuoiPzWJ6teo2CCEkoFBJZlLXOK6X9OSv\r\nO9yFEd9PVU4BrbAxHf9q9HHrh6SMKcdWTfpjL6LbydVIZYXdr2JC/aRF5hO9lxTj\r\nc5PKdB9GCPt3S0ad0n2jdRJy0j1Q2rMFaTJEWoR7SPeFxas=\r\n-----END CERTIFICATE-----'
        issuer = 'bad issuer'
        
        with self.assertRaises(ValueError):
            result = extract_claims_from_signed_token(id_token, signature, issuer)
