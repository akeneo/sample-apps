use crate::model::token::Token;
use anyhow::{anyhow, Result};

#[derive(Debug)]
pub struct FirstApiCallRequest {
    pub token: Token,
}

const FIRST_API_CALL_URL: &str = "/api/rest/v1/channels";

impl FirstApiCallRequest {
    pub async fn execute(&self, pim_url: String) -> Result<String> {
        let client = reqwest::Client::new();

        let response = client
            .get(&format!("{}{}", pim_url, FIRST_API_CALL_URL))
            .bearer_auth(&self.token.access_token)
            .send()
            .await?;

        match response.text().await {
            Ok(response_body) => Ok(response_body),
            Err(_) => Err(anyhow!("Error while calling the API")),
        }
    }
}
