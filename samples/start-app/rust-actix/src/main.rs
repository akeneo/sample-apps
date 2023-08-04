use anyhow::Result;
use rust_actix::application::Application;
use rust_actix::configuration::Settings;

#[tokio::main]
async fn main() -> Result<()> {
    let setting = Settings::get(None);
    let application = Application::build(&setting).expect("Failed to build application");

    tokio::spawn(application.server);

    Ok(())
}
