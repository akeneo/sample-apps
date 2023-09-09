use std::{env, fs};

#[derive(Debug, Clone)]
pub struct Settings {
    pub app_server_addr: String,
    pub pim_url: String,
    pub client_id: String,
    pub client_secret: String,
    pub scopes: String,
    pub secure_cookie: bool,
    pub session_key: String,
    pub app_name: String,
    pub log_level: String,
    pub sub_hash_key: String,
}

// https://api.akeneo.com/apps/authentication-and-authorization.html#authorization-and-authentication-scopes
static PIM_AUTHORIZATION_SCOPES: &str =
    "openid email profile read_channel_localization read_channel_settings";

impl Settings {
    pub fn get(path: Option<String>) -> Self {
        self::Settings::load_env_var(path);

        let app_host = env::var("APP_HOST").expect("APP_HOST is not set in .env file");
        let app_port = env::var("APP_PORT").expect("APP_PORT is not set in .env file");
        let session_key = env::var("SESSION_KEY").expect("SESSION_KEY is not set in .env file");
        let scopes = env::var("SCOPES").unwrap_or(PIM_AUTHORIZATION_SCOPES.to_string());

        if session_key.len() < 64 {
            panic!("SESSION_KEY must be at least 64 characters long");
        }

        Self {
            app_server_addr: format!("{app_host}:{app_port}"),
            pim_url: env::var("PIM_URL").expect("PIM_URL is not set in .env file"),
            client_id: env::var("CLIENT_ID").expect("CLIENT_ID is not set in .env file"),
            client_secret: env::var("CLIENT_SECRET")
                .expect("CLIENT_SECRET is not set in .env file"),
            scopes,
            secure_cookie: env::var("SECURE_COOKIE")
                .expect("SECURE_COOKIE is not set in .env file")
                .parse::<bool>()
                .expect("SECURE_COOKIE is not a boolean"),
            session_key,
            app_name: env::var("APP_NAME").unwrap_or_else(|_| "sample-app".into()),
            log_level: env::var("LOG_LEVEL").unwrap_or_else(|_| "error".into()),
            sub_hash_key: env::var("SUB_HASH_KEY").expect("SUB_HASH_KEY is not set in .env file"),
        }
    }

    fn load_env_var(path: Option<String>) {
        match path {
            Some(path) => {
                dotenv::from_filename(path).ok();
            }
            None => {
                match fs::metadata(".env.local") {
                    Err(_) => dotenv::dotenv().ok(),
                    Ok(_) => dotenv::from_filename(".env.local").ok(),
                };
            }
        };
    }
}
