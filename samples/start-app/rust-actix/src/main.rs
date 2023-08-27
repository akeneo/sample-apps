use anyhow::Result;
use rust_actix::application::Application;
use rust_actix::configuration::Settings;
use rust_actix::logger::init_subscriber;
use rust_actix::database::init_database;

#[tokio::main]
async fn main() -> Result<()> {
    // Initialize database and get a connection pool
    let pool = init_database("rust_actix.db".to_string())?;
    // Load configuration
    let settings = Settings::get(None);
    // Initialize logger
    init_subscriber(settings.app_name.clone(), settings.log_level.clone());

    let application = Application::build(settings, pool).expect("Failed to build application");

    let _ = application.server.await;

    Ok(())
}
