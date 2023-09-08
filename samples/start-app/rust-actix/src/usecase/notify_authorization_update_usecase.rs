use crate::model::token::Token;
use anyhow::{anyhow, Result};

#[derive(Debug)]
pub struct NotifyAuthorizationRequest {
    pub token: Token,
}

static NOTIFICATION_AUTHORIZATION_UPDATE_URL: &str =
    "/connect/apps/v1/scopes/update?scopes=";

impl NotifyAuthorizationRequest {
    pub async fn execute(&self, pim_url: String, scopes: String) -> Result<String> {
        let client = reqwest::Client::new();

        let response = client
            .post(&format!(
                "{}{}{}",
                pim_url, NOTIFICATION_AUTHORIZATION_UPDATE_URL, scopes
            ))
            .bearer_auth(&self.token.access_token)
            .send()
            .await?;

        match response.text().await {
            Ok(response_body) => Ok(response_body),
            Err(_) => Err(anyhow!("Error while calling the API")),
        }
    }
}
