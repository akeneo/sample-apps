use actix_web::{get, HttpResponse, Responder, web};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use sailfish::TemplateOnce;

use crate::template::{AccessToken, NoAccessToken};
use crate::model::token::{Token, TokenRepository};


#[tracing::instrument(
    name = "Index"
)]
#[get("/")]
async fn index(
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {

    // Check if we have a token in DB
    match Token::exists(&pool).await {
        Ok(_) => {
            let ctx = AccessToken { user: None };
            return HttpResponse::Ok().body(
                ctx.render_once().unwrap(),
            )
        },
        Err(_) => {
            let ctx = NoAccessToken {};

            return HttpResponse::Ok().body(
                ctx.render_once().unwrap(),
            )    
        }
    };
}
