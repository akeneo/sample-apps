use actix_session::Session;
use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use serde::Deserialize;
use tracing::{event, Level};

use crate::{
    application::{callback_usecase::CallbackAuthorizationRequest, openid_connect::IdToken},
    configuration::Settings,
    model::token::{self, Token, TokenRepository},
    model::user::{self, UserRepository},
};

const ERROR_MISSING_STATE: &str = "Invalid Session information, missing state";
const ERROR_MISSING_PIM_URL: &str = "Invalid Session information, missing pim_url";
const ERROR_INVALID_STATE: &str = "Invalid state";

#[derive(Debug, Deserialize)]
pub struct CallbackRequest {
    pub state: String,
    pub code: String,
}

#[tracing::instrument(
    name = "Callback", 
    skip(session, data, callback_request),
    fields(code = %callback_request.code, state = %callback_request.state)
)]
#[get("/callback")]
async fn callback(
    session: Session,
    data: web::Data<Settings>,
    callback_request: web::Query<CallbackRequest>,
    pool: web::Data<Pool<SqliteConnectionManager>>,
) -> impl Responder {
    // We check if the received state is the same as in the session, for security.
    let state = match session.get::<String>("state").unwrap() {
        None => {
            event!(Level::ERROR, ERROR_MISSING_STATE);
            return HttpResponse::BadRequest().body(ERROR_MISSING_STATE);
        }
        Some(s) => s,
    };

    if state != callback_request.state {
        event!(
            Level::ERROR,
            ERROR_INVALID_STATE,
            received_state = callback_request.state,
            expected_state = state
        );
        return HttpResponse::BadRequest().body(ERROR_INVALID_STATE);
    }

    let pim_url = match session.get::<String>("pim_url").unwrap() {
        None => {
            event!(Level::ERROR, ERROR_MISSING_PIM_URL);
            return HttpResponse::BadRequest().body(ERROR_MISSING_PIM_URL);
        }
        Some(p) => p,
    };

    // Call the PIM API to get the access tokens
    let authorization_request = CallbackAuthorizationRequest {
        pim_url: pim_url.clone(),
        code: callback_request.code.clone(),
        client_id: data.client_id.clone(),
        client_secret: data.client_secret.clone(),
    };

    let authorization = authorization_request.execute().await.unwrap();
    let access_token = authorization.access_token.clone();
    let id_token = authorization.id_token.clone();

    // Save the access token in the database
    token::Token::save(
        &pool,
        Token {
            access_token: access_token.clone(),
        },
    )
    .await
    .unwrap();

    // If we have an id_token, we can extract the user information from it
    if id_token != None {
        // Get the user information from the id_token
        let token = IdToken {
            id_token: id_token.unwrap().to_string(),
        };

        let user = IdToken::get_user(&token, &pim_url).await.unwrap();

        // Save the user information in the database
        user::User::save(&pool, user.clone()).await.unwrap();

        return HttpResponse::Ok().body(format!("User : {:?}", user));
    }

    HttpResponse::Ok().body(format!("Token : {}", access_token))
}
