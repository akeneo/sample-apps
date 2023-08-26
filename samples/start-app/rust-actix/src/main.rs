use anyhow::Result;
use rust_actix::application::Application;
use rust_actix::configuration::Settings;
use rust_actix::logger::init_subscriber;

#[tokio::main]
async fn main() -> Result<()> {
    let settings = Settings::get(None);
    // Initialize logger
    init_subscriber(settings.app_name.clone(),settings.log_level.clone());

    let application = Application::build(settings).expect("Failed to build application");

    let _ = application.server.await;

    Ok(())
}
