use actix_session::Session;
use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use sailfish::TemplateOnce;

use crate::configuration::Settings;
use crate::model::token::{Token, TokenRepository};
use crate::model::user::{self, UserRepository};
use crate::template::{AccessToken, NoAccessToken};

#[tracing::instrument(
    name = "Index"
    skip(session, data, pool)
)]
#[get("/")]
async fn index(
    session: Session,
    data: web::Data<Settings>,
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {
    // Check if we have a token in DB
    match Token::exists(&pool).await {
        Ok(_) => {
            let mut ctx = AccessToken {
                sub: "".to_string(),
                email: "".to_string(),
            };
            // Authenticate User with his cookie
            let sub = session.get::<String>("sub").unwrap_or(None);
            let vector = session.get::<String>("vector").unwrap_or(None);

            if sub.is_some() && vector.is_some() && vector.unwrap() == data.sub_hash_key.clone() {
                // Get user from DB
                if let Ok(user) = user::User::find_by_sub(&pool, sub.unwrap()).await {
                    ctx.sub = user.sub;
                    ctx.email = user.email;
                }
            };

            HttpResponse::Ok().body(ctx.render_once().unwrap())
        }
        Err(_) => {
            let ctx = NoAccessToken {};

            HttpResponse::Ok().body(ctx.render_once().unwrap())
        }
    }
}
