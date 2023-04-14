import unittest
from unittest.mock import MagicMock, patch
from fastapi import Request
from sqlalchemy.orm import Session
from app.usecase.callback import callback_usecase
from app.dependencies import get_config, build_user_agent

class TestCallbackUsecase(unittest.TestCase):

    def setUp(self):
        self.request = MagicMock(spec=Request)
        self.db = MagicMock(spec=Session)
        self.session = MagicMock()
        self.session.headers = {'oauth2_state': 'state_value', 'pim_url': 'http://pim-url.com'}
        self.request.query_params = {'state': 'state_value', 'code': 'auth_code'}

    @patch('app.persistence.tokenRepository.create_token')
    @patch('requests.post')
    @patch('secrets.token_hex')
    @patch('hashlib.sha256')
    @patch('app.usecase.callback.get_config')
    def test_callback_usecase(self, mock_get_config, mock_sha256, mock_token_hex, mock_post, mock_create_token):
        # Arrange
        mock_post.return_value.json.return_value = {'access_token': 'access_token_value'}
        mock_sha256.return_value.hexdigest.return_value = 'd9252f527368ffee07ffe7d9c2722d1e244fc94a8e79e938b8e5a815584942cb'
        mock_token_hex.return_value = '4d713c758a30768e5576cb7b6f3b2dad62e7bdb5fd4a6132b11ca6301824'
        mock_get_config.return_value = '123456'
        # Act
        response = callback_usecase(self.request, self.db, self.session)
        # Assert
        mock_post.assert_called_once_with(
            'http://pim-url.com/connect/apps/v1/oauth2/token',
            data={
                'client_id': '123456',
                'code_identifier': '4d713c758a30768e5576cb7b6f3b2dad62e7bdb5fd4a6132b11ca6301824', 
                'code_challenge': 'd9252f527368ffee07ffe7d9c2722d1e244fc94a8e79e938b8e5a815584942cb',
                'code': 'auth_code',
                'grant_type': 'authorization_code'
            },
            headers={
                'Content-Type': 'application/x-www-form-urlencoded',
                'User-Agent': build_user_agent()
            }
        )
        mock_create_token.assert_called_once_with(
            db=self.db,
            token={'access_token': 'access_token_value'}
        )
        self.assertEqual(response.headers, mock_post.return_value.headers)

    def test_callback_usecase_invalid_state(self):
        # Arrange
        self.request.query_params['state'] = 'invalid_state_value'
        # Act and Assert
        with self.assertRaises(Exception) as e:
            callback_usecase(self.request, self.db, self.session)
        self.assertEqual(str(e.exception), 'Invalide state')

    def test_callback_usecase_missing_auth_code(self):
        # Arrange
        self.request.query_params['code'] = ''
        # Act and Assert
        with self.assertRaises(Exception) as e:
            callback_usecase(self.request, self.db, self.session)
        self.assertEqual(str(e.exception), 'Missing authorization code')

    def test_callback_usecase_missing_pim_url(self):
        # Arrange
        self.session.headers['pim_url'] = ''
        # Act and Assert
        with self.assertRaises(Exception) as e:
            callback_usecase(self.request, self.db, self.session)
        self.assertEqual(str(e.exception), 'No PIM url in session')
