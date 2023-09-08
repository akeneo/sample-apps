use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use tracing::event;

use crate::{
    configuration::Settings,
    model::token::{Token, TokenRepository},
    usecase::notify_authorization_update_usecase::NotifyAuthorizationRequest,
};

#[tracing::instrument(
    name = "NotifyAuthorizationUpdate"
    skip(data)
)]
#[get("/notify-authorization-update")]
async fn notify_authorization_update(
    data: web::Data<Settings>,
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {
    match Token::get(&pool).await {
        Ok(token) => {
            let notify_authorization_update_request = NotifyAuthorizationRequest { token };

            match notify_authorization_update_request
                .execute(data.pim_url.clone(), data.scopes.clone())
                .await
            {
                Ok(response) => HttpResponse::Ok()
                    .content_type("application/json")
                    .body(response),
                Err(_) => {
                    event!(tracing::Level::ERROR, "Error while calling the API");
                    HttpResponse::InternalServerError().body("Error while calling the API")
                }
            }
        }
        _ => {
            event!(tracing::Level::DEBUG, "No token found");
            HttpResponse::InternalServerError().body("No token found")
        }
    }
}
