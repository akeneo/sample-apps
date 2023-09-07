use actix_web::{get, HttpResponse, Responder, web, http::header};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;

use crate::{model::token::{Token, TokenRepository}, usecase::first_api_callback_usecase::FirstApiCallRequest};


#[tracing::instrument(
    name = "FirstApiCall"
)]
#[get("/first-api-call")]
async fn first_api_call(
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {
    match Token::get(&pool).await {
        Ok(token) => {
            // Call API
            let first_api_request = FirstApiCallRequest { token };

            match first_api_request.execute() {
                Ok(response) => {
                    println!(response)
                },
                Err(_) => {}
            }

        },
        Err(_) => {}
    };    

    // Redirect to the home page
    HttpResponse::Found()
        .insert_header((header::LOCATION, "/"))
        .finish()
}
