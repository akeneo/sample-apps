use rust_actix::application::Application;
use rust_actix::configuration::Settings;

#[derive(Debug)]
pub struct TestApp {
    pub address: String,
}

impl TestApp {
    pub async fn spawn_app() -> Self {
        let settings = Settings::get(Some("tests/features/.env.test".to_string()));
        let application = Application::build(&settings).expect("Failed to build application");
        let address = format!("http://{}", &application.address);

        tokio::spawn(application.server);

        Self { address }
    }
}
