use dotenv::dotenv;
use std::env;

#[derive(Debug, Clone)]
pub struct Settings {
    pub app_server_addr: String,
    pub pim_url: String,
    pub pim_client_id: String,
    pub pim_client_secret: String,
}

impl Settings {
    pub fn get(path: Option<String>) -> Self {
        self::Settings::load_env_var(path);

        let app_host = env::var("APP_HOST").expect("APP_HOST is not set in .env file");
        let app_port = env::var("APP_PORT").expect("APP_PORT is not set in .env file");

        Self {
            app_server_addr: format!("{app_host}:{app_port}"),
            pim_url: env::var("PIM_URL").expect("PIM_URL is not set in .env file"),
            pim_client_id: env::var("PIM_CLIENT_ID")
                .expect("PIM_CLIENT_ID is not set in .env file"),
            pim_client_secret: env::var("PIM_CLIENT_SECRET")
                .expect("PIM_CLIENT_SECRET is not set in .env file"),
        }
    }

    fn load_env_var(path: Option<String>) {
        match path {
            Some(path) => {
                dotenv::from_filename(path).ok();
            }
            None => {
                dotenv().ok();
                dotenv::from_filename(".env.local").ok();
            }
        };
    }
}
