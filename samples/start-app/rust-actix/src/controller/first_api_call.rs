use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use tracing::event;

use crate::{
    configuration::Settings,
    model::token::{Token, TokenRepository},
    usecase::first_api_callback_usecase::FirstApiCallRequest,
};

#[tracing::instrument(
    name = "FirstApiCall"
    skip(data)
)]
#[get("/first-api-call")]
async fn first_api_call(
    data: web::Data<Settings>,
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {
    match Token::get(&pool).await {
        Ok(token) => {
            let first_api_request = FirstApiCallRequest { token };

            match first_api_request.execute(&data.pim_url).await {
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
