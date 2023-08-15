use anyhow::Result;
use rand::distributions::{Alphanumeric, DistString};
use sha2::{Sha256,Digest};

pub struct CallbackAuthorizationRequest {
    pub pim_url: String,
    pub code: String,
    pub client_id: String,
    pub client_secret: String,
}


impl CallbackAuthorizationRequest {
    pub async fn execute(&self) -> Result<()> {
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

        let response = client.post(format!("{}/oauth/token", self.pim_url))
            .form(&params)
            .send()
            .await?;
        
        println!("{:?}", response.text().await?);

        Ok(())
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