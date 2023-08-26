use reqwest::Url;
use serde::Deserialize;
use tracing::event;

use crate::application::activate::ActivateRequest;

static PIM_AUTHORIZATION_SCOPES : &str = "openid email profile read_channel_localization read_channel_settings";
static PIM_AUTHORIZATION_URL : &str = "/connect/apps/v1/authorize";

#[derive(Debug, Deserialize)]
pub struct ActivateResponse {
    pub redirect_uri: String,
}
    
impl ActivateResponse {
    pub fn build(
        active_resquest: ActivateRequest, 
        client_id: String,
        state: String,
    ) -> Self {
        event!(tracing::Level::DEBUG, "Building authorize PIM Url");

        let url = format!("{}{}?response_type={}&client_id={}&scope={}&state={}",
            Self::remove_last_slash(&active_resquest.pim_url.to_string()), 
            PIM_AUTHORIZATION_URL, 
            "code".to_string(),
            client_id,
            PIM_AUTHORIZATION_SCOPES.to_string(),
            state,
        );

        ActivateResponse {
            redirect_uri: Url::parse(&url).unwrap().to_string(),
        }
    }

    fn remove_last_slash(url: &str) -> String {
        let mut url = url.to_string();
        if url.ends_with("/") {
            url.pop();
        }
        url
    }    
}