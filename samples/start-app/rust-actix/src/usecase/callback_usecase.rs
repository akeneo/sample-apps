use anyhow::Result;
use rand::distributions::{Alphanumeric, DistString};
use reqwest::StatusCode;
use serde::Deserialize;
use sha2::{Digest, Sha256};
use tracing::event;

#[derive(Debug)]
pub struct CallbackAuthorizationRequest {
    pub pim_url: String,
    pub code: String,
    pub client_id: String,
    pub client_secret: String,
}

#[derive(Debug, Deserialize)]
pub struct AuthorizationResponse {
    pub access_token: String,
    pub token_type: String,
    pub scope: String,
    pub id_token: Option<String>,
}

const PIM_API_AURHORIZATION_ENDPOINT: &str = "/connect/apps/v1/oauth2/token";

impl CallbackAuthorizationRequest {
    pub async fn execute(&self) -> Result<AuthorizationResponse> {
        let client = reqwest::Client::new();
        let mut params = std::collections::HashMap::new();

        let code_identifier = self.code_identifier();
        let code_challenge = self.code_challenge(&code_identifier);
        let grant_type = "authorization_code".to_string();

        params.insert("client_id", &self.client_id);
        params.insert("code_identifier", &code_identifier);
        params.insert("code_challenge", &code_challenge);
        params.insert("code", &self.code);
        params.insert("grant_type", &grant_type);

        event!(tracing::Level::DEBUG, "Requesting access token");

        let response = client
            .post(format!(
                "{}{}",
                self.pim_url.trim_end_matches('/'),
                PIM_API_AURHORIZATION_ENDPOINT
            ))
            .form(&params)
            .send()
            .await?;

        let response_status = response.status();

        if response_status == StatusCode::OK {
            let response_body = response.text().await?;
            let authorization_response: AuthorizationResponse =
                serde_json::from_str(&response_body)?;
            event!(tracing::Level::DEBUG, "Access token received");
            Ok(authorization_response)
        } else {
            event!(tracing::Level::ERROR, "Error while requesting access token");
            Err(anyhow::anyhow!("Error while requesting access token"))
        }
    }

    fn code_identifier(&self) -> String {
        hex::encode(Alphanumeric.sample_string(&mut rand::thread_rng(), 30))
    }

    fn code_challenge(&self, code_identifier: &String) -> String {
        let mut hasher = Sha256::new();
        let to_hash = code_identifier.to_owned() + self.client_secret.as_str();
        hasher.update(to_hash.as_bytes());
        let result = hasher.finalize();
        hex::encode(result)
    }
}
