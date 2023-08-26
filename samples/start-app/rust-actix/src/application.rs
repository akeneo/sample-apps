use std::net::TcpListener;

use actix_session::config::PersistentSession;
use actix_session::{SessionMiddleware, storage::CookieSessionStore};
use actix_web::cookie::{self, Key};
use actix_web::{dev::Server, App, HttpServer};

use crate::configuration::Settings;
pub use crate::controller::*;
pub use crate::usecase::*;

pub struct Application {
    pub address: String,
    pub server: Server,
}

impl Application {
    pub fn build(settings: Settings) -> anyhow::Result<Self> {
        let listener =
            TcpListener::bind(settings.app_server_addr.as_str()).expect("Failed to bind address");    
        let address = listener.local_addr().unwrap().to_string();
        let server = HttpServer::new(move || {
            App::new()
                .app_data(
                    actix_web::web::Data::new(settings.clone()),
                )
                // We can log every request that comes in using the TracingLogger middleware
                // .wrap(
                //     TracingLogger::default(),
                // )
                .wrap(
                    SessionMiddleware::builder(CookieSessionStore::default(), Key::generate())
                    .cookie_secure(settings.secure_cookie)
                    .cookie_name("SAMPLE-APP-ID".to_string())
                    .session_lifecycle(
                        PersistentSession::default().session_ttl(cookie::time::Duration::hours(1)),
                    )
                    .build(),
                )
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
