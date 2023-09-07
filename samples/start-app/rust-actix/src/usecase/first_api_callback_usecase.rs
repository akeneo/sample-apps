use crate::model::token::Token;
use anyhow::Result;

#[derive(Debug)]
pub struct FirstApiCallRequest {
    pub token: Token
}

impl FirstApiCallRequest {
    pub async fn execute(&self) -> Result<()> {
        // Call API
        let client = reqwest::Client::new();
        

        Ok(())
    }
}

