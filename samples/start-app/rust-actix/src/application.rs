use std::net::TcpListener;

use actix_web::{dev::Server, App, HttpServer};

use crate::configuration::Settings;
pub use crate::controller::*;

pub struct Application {
    pub address: String,
    pub server: Server,
}

impl Application {
    pub fn build(settings: &Settings) -> anyhow::Result<Self> {
        let listener =
            TcpListener::bind(settings.app_server_addr.as_str()).expect("Failed to bind address");
        let address = listener.local_addr().unwrap().to_string();
        let server = HttpServer::new(move || {
            App::new()
                .service(health_check::health_check)
                .service(index::index)
                .service(callback::callback)
                .service(activate::activate)
                .service(first_api_call::first_api_call)
                .service(notify_authorization_update::notify_authorization_update)
        })
        .listen(listener)?
        .run();

        Ok(Self { address, server })
    }
}
