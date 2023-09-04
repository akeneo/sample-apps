use actix_web::{get, HttpResponse, Responder, web};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use sailfish::TemplateOnce;

use crate::model::token::{Token, TokenRepository};


#[derive(TemplateOnce)]
#[template(path = "./access_token.stpl")]
struct AccessTokenTemplate {
    
}

#[derive(TemplateOnce)]
#[template(path = "./no_access_token.stpl")]
struct NoAccessTokenTemplate {
    
}


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
            let ctx = AccessTokenTemplate {};
            return HttpResponse::Ok().body(
                ctx.render_once().unwrap(),
            )
        },
        Err(_) => {
            let ctx = NoAccessTokenTemplate {};

            return HttpResponse::Ok().body(
                ctx.render_once().unwrap(),
            )    
        }
    };
}
