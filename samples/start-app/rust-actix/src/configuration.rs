use std::{env, fs};

#[derive(Debug, Clone)]
pub struct Settings {
    pub app_server_addr: String,
    pub client_id: String,
    pub client_secret: String,
    pub secure_cookie: bool,
    pub app_name: String,
    pub log_level: String,
}

impl Settings {
    pub fn get(path: Option<String>) -> Self {
        self::Settings::load_env_var(path);

        let app_host = env::var("APP_HOST").expect("APP_HOST is not set in .env file");
        let app_port = env::var("APP_PORT").expect("APP_PORT is not set in .env file");

        Self {
            app_server_addr: format!("{app_host}:{app_port}"),
            client_id: env::var("CLIENT_ID")
                .expect("CLIENT_ID is not set in .env file"),
            client_secret: env::var("CLIENT_SECRET")
                .expect("CLIENT_SECRET is not set in .env file"),
            secure_cookie: env::var("SECURE_COOKIE")
                .expect("SECURE_COOKIE is not set in .env file")
                .parse::<bool>()
                .expect("SECURE_COOKIE is not a boolean"),
            app_name: env::var("APP_NAME").unwrap_or_else(|_| "sample-app".into()),
            log_level: env::var("LOG_LEVEL").unwrap_or_else(|_| "error".into()),
        }
    }

    fn load_env_var(path: Option<String>) {
        match path {
            Some(path) => {
                dotenv::from_filename(path).ok();
            }
            None => {
                match fs::metadata(".env.local") {
                    Err(_) => {
                        dotenv::dotenv().ok()
                    },
                    Ok(_) => {
                        dotenv::from_filename(".env.local").ok()
                    }
                };
            }
        };
    }
}
