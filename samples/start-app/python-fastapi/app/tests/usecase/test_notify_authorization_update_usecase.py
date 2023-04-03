import unittest
from unittest.mock import Mock
from app.persistence import tokenRepository
from app.dependencies import get_config, build_user_agent
from app.usecase.activate import oauth_scopes
from app.usecase.notifyAuthorizationUpdate import notify_authorization_update_usecase
import urllib.parse
from urllib.parse import urljoin
import requests


class TestNotifyAuthorizationUpdateUseCase(unittest.TestCase):
    def setUp(self):
        self.db = Mock()
        self.api_url = urljoin(get_config('AKENEO_PIM_URL'), '/connect/apps/v1/scopes/update?scopes=')
        self.api_url = self.api_url + urllib.parse.quote(' '.join(oauth_scopes))
        self.token = Mock()
        tokenRepository.get_token = Mock(return_value=self.token)
        self.response = Mock()
        requests.post = Mock(return_value=self.response)

    def test_notify_authorization_update_usecase(self):
        self.token.access_token = 'dummy_access_token'
        expected_headers = {
            'Authorization': 'Bearer dummy_access_token',
            'User-Agent': build_user_agent()
        }
        expected_response = self.response
        actual_response = notify_authorization_update_usecase(self.db)
        requests.post.assert_called_once_with(self.api_url, headers=expected_headers)
        self.assertEqual(actual_response, expected_response)
