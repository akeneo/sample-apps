use actix_session::Session;
use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use serde::Deserialize;
use tracing::{event, Level};

use crate::{
    application::callback_usecase::CallbackAuthorizationRequest,
    configuration::Settings,
    model::token::{self, Token, TokenRepository},
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
        pim_url,
        code: callback_request.code.clone(),
        client_id: data.client_id.clone(),
        client_secret: data.client_secret.clone(),
    };

    let authorization = authorization_request.execute().await.unwrap();
    let access_token = authorization.access_token.clone();

    // Save the access token in the database
    let conn = pool.get().unwrap();
    token::Token::save(
        conn,
        Token {
            access_token: access_token.clone(),
        },
    )
    .unwrap();

    // If we have an id_token, we can extract the user information from it
    if Some(authorization.id_token) != None {
        // TODO: extract user information from id_token

        // Save the user information in the database
    }

    HttpResponse::Ok().body(format!("Access token : {access_token}"))
}
