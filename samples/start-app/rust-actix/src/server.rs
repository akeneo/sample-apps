use actix_web::{App, HttpServer};
use dotenv::dotenv;
use std::env;

pub use crate::controller::*;

pub fn run_server() -> Result<actix_web::dev::Server, std::io::Error> {
    dotenv().ok();

    let app_host = env::var("APP_HOST").expect("APP_HOST is not set in .env file");
    let app_port = env::var("APP_PORT").expect("APP_PORT is not set in .env file");
    let app_server_addr = format!("{app_host}:{app_port}");

    // Let's start HTTP server
    let server = HttpServer::new(move || {
        App::new()
            .service(health_check::health_check)
            .service(index::index)
            .service(callback::callback)
            .service(activate::activate)
            .service(first_api_call::first_api_call)
            .service(notify_authorization_update::notify_authorization_update)
    })
    .bind(app_server_addr.as_str())?
    .run();

    Ok(server)
}
