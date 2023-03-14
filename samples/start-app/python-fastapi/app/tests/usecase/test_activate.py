import unittest
from unittest.mock import patch, MagicMock
from app.usecase.activate import activate_usecase
from fastapi import Request
from app.dependencies import get_config


class TestCallbackUsecase(unittest.TestCase):

    def setUp(self):
        self.request = MagicMock(spec=Request)
        self.request.query_params = {'pim_url': 'http://example.com'}
        self.session = MagicMock()
        self.session.headers = {}

    @patch('secrets.token_hex')
    @patch('urllib.parse.urlencode')
    def test_activate_usecase(self, mock_urlencode, mock_token_hex):
        mock_urlencode.return_value = 'response_type=code&client_id=test_client_id&scope=read_channel_localization read_channel_settings&state=test_state'
        mock_token_hex.return_value = 'test_state'

        result = activate_usecase(self.request, self.session)

        mock_urlencode.assert_called_once_with({
            'response_type': 'code',
            'client_id': get_config('CLIENT_ID'),
            'scope': 'read_channel_localization read_channel_settings',
            'state': 'test_state'
        })
        mock_token_hex.assert_called_once_with(10)

        self.assertEqual(result, 'http://example.com/connect/apps/v1/authorize?response_type=code&client_id=test_client_id&scope=read_channel_localization read_channel_settings&state=test_state')
        self.assertEqual(self.session.headers['oauth2_state'], 'test_state')
        self.assertEqual(self.session.headers['pim_url'], 'http://example.com')

    def test_activate_usecase_missing_pim_url(self):
        self.request.query_params = {}
        with self.assertRaises(Exception) as context:
            activate_usecase(self.request, self.session)

        self.assertEqual(str(context.exception), 'Missing PIM URL in the query')
        self.assertEqual(self.session.headers, {})
