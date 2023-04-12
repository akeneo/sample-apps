import unittest
from unittest.mock import MagicMock, patch
from app.persistence import tokenRepository
from app.dependencies import build_user_agent
from urllib.parse import urljoin
from app.usecase.firstApiCall import first_api_call_usecase
import requests

class TestApiCalls(unittest.TestCase):

    def setUp(self):
        self.db = MagicMock()
        self.token = MagicMock()
        self.token.access_token = 'test_token'

    @patch('app.usecase.firstApiCall.get_config')
    def test_first_api_call_usecase(self, mock_get_config):
        pimUrl = 'https://pim-url.com'
        mock_get_config.return_value = pimUrl

        api_url = pimUrl  + '/api/rest/v1/channels'
        expected_response = {
            "_links": {
                "self": {
                    "href": api_url + "?page=1&limit=10&with_count=false"
                },
                "first": {
                    "href": api_url + "api/rest/v1/channels?page=1&limit=10&with_count=false"
                }
            },
            "current_page": 1,
            "_embedded": {
                "_links": {
                "self": {
                    "href": api_url + "api/rest/v1/channels/akeneo_onboarder_channel"
                }
                },
                "code": "akeneo_onboarder_channel",
                "currencies": [
                    "USD"
                ],
                "locales": [
                    "de_DE",
                    "en_GB",
                    "en_US",
                    "fr_FR",
                    "ja_JP"
                ],
                "category_tree": "master",
                "conversion_units": {},
                "labels": {
                    "en_US": "Ecommerce",
                    "de_DE": "Ecommerce",
                    "fr_FR": "Ecommerce"
                }
            }
            }

        # Mock tokenRepository.get_token to return a mocked token
        tokenRepository.get_token = MagicMock(return_value=self.token)

        # Mock requests.get to return the expected response
        requests.get = MagicMock(return_value=expected_response)

        # Call the function and assert that the response is the expected response
        response = first_api_call_usecase(self.db)
        self.assertEqual(response, expected_response)

        # Assert that the mocked functions were called with the expected arguments
        tokenRepository.get_token.assert_called_once_with(self.db)
        requests.get.assert_called_once_with(
            api_url,
            headers={
                'Authorization': 'Bearer test_token',
                'User-Agent': build_user_agent()
            }
        )

