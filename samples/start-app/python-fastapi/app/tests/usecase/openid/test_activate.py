import unittest
from unittest.mock import MagicMock, patch
from app.dependencies import get_config
from fastapi import Request
from app.usecase.openid.activate import activate_usecase_with_openid

class TestActivateUsecaseWithOpenid(unittest.TestCase):

    def setUp(self):
        self.request = MagicMock(spec=Request)
        self.session = MagicMock()
        self.session.headers = {'oauth2_state': 'state_value', 'pim_url': 'http://pim-url.com'}
        self.request.query_params = {'state': 'state_value', 'code': 'auth_code'}

    @patch('app.usecase.openid.activate.activate_usecase')
    def test_activate_usecase_with_openid_calls_activate_usecase_with_correct_arguments(self, mock_activate_usecase):
        result = activate_usecase_with_openid(self.request, self.session)
        expected_scopes = [
            'read_channel_localization',
            'read_channel_settings',
            'openid',
            'email',
            'profile'
        ]
        mock_activate_usecase.assert_called_once_with(self.request, self.session, expected_scopes)
        