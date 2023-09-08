use anyhow::Result;
use jsonwebtoken::{decode, Algorithm, DecodingKey, Validation};
use reqwest::StatusCode;
use serde::{Deserialize, Serialize};

use crate::model::user::User;

#[derive(Debug, Serialize, Deserialize)]
pub struct IdToken {
    pub id_token: String,
}

#[derive(Debug, Serialize, Deserialize)]
struct PubliKey {
    public_key: String,
}

#[derive(Debug, Serialize, Deserialize)]
struct Claims {
    sub: String,
    iss: String,
    aud: String,
    jti: String,
    iat: f32,
    exp: f32,
    firstname: Option<String>,
    lastname: Option<String>,
    email: Option<String>,
    email_verified: Option<bool>,
}

static PIM_API_OPENID_PUBLIC_KEY: &str = "/connect/apps/v1/openid/public-key";

impl IdToken {
    pub async fn get_user(&self, pim_url: &str) -> Result<User> {
        let public_key_content = fetch_public_key(pim_url).await?;

        let token = decode::<Claims>(
            &self.id_token,
            &DecodingKey::from_rsa_pem(public_key_content.public_key.as_bytes())?,
            &Validation::new(Algorithm::RS256),
        )?;

        let claims = token.claims;

        Ok(User {
            sub: claims.sub,
            firstname: claims.firstname.unwrap_or("".to_string()),
            lastname: claims.lastname.unwrap_or("".to_string()),
            email: claims.email.unwrap_or("".to_string()),
        })
    }
}

async fn fetch_public_key(pim_url: &str) -> Result<PubliKey> {
    let client = reqwest::Client::new();
    let response = client
        .get(format!(
            "{}{}",
            pim_url.trim_end_matches('/'),
            PIM_API_OPENID_PUBLIC_KEY
        ))
        .send()
        .await?;

    let response_status = response.status();

    if response_status == StatusCode::OK {
        let response_body = response.text().await?;
        let public_key: PubliKey = serde_json::from_str(&response_body)?;

        Ok(public_key)
    } else {
        Err(anyhow::anyhow!("Error while requesting public key"))
    }
}
