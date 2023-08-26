use once_cell::sync::Lazy;
use rust_actix::application::Application;
use rust_actix::configuration::Settings;
use rust_actix::logger::init_subscriber;

#[derive(Debug)]
pub struct TestApp {
    pub address: String,
}

// Ensure that the `tracing` stack is only initialised once using `once_cell`
static TRACING: Lazy<()> = Lazy::new(|| {
    let subscriber_name = "test".into();
    let default_filter_level = "debug".into();
    init_subscriber(subscriber_name, default_filter_level)
});

impl TestApp {
    pub async fn spawn_app() -> Self {
        Lazy::force(&TRACING);

        let settings = Settings::get(Some("tests/features/.env.test".to_string()));
        let application = Application::build(settings).expect("Failed to build application");
        let address = format!("http://{}", &application.address);

        tokio::spawn(application.server);

        Self { address }
    }
}
