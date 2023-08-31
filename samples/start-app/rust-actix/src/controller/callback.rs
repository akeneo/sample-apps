use actix_session::Session;
use actix_web::{get, web, HttpResponse, Responder};
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use reqwest::StatusCode;
use serde::Deserialize;
use tracing::{event, Level};

use crate::{
        application::callback_usecase::CallbackAuthorizationRequest, 
        configuration::Settings, 
        model::token::{self, Token, TokenRepository}
    };

const ERROR_MISSING_STATE: &str = "Invalid Session information, missing state";
const ERROR_MISSING_PIM_URL: &str = "Invalid Session information, missing pim_url";
const ERROR_INVALID_STATE: &str = "Invalid state";
const ERROR_INVALID_REQUEST: &str = "Invalid request";

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
    pool: web::Data<Pool<SqliteConnectionManager>>
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

    let authorization_request = CallbackAuthorizationRequest {
        pim_url,
        code: callback_request.code.clone(),
        client_id: data.client_id.clone(),
        client_secret: data.client_secret.clone(),
    };

    let (status_code, content) = authorization_request.execute().await.unwrap();

    if status_code != StatusCode::OK {
        event!(
            Level::ERROR,
            ERROR_INVALID_REQUEST,
            status_code = status_code.as_u16(),
        );
        return HttpResponse::BadRequest().body(ERROR_INVALID_REQUEST);
    }
    
    if content.contains("access_token") {
        event!(Level::INFO, "Access token received");
        // TODO: Store the access token in the sqlite database
        let conn = pool.get().unwrap();
        token::Token::save(conn, Token {
            access_token: "test".to_string()
        }).unwrap()

    } else {
        event!(Level::ERROR, "Access token not received");
    }

    HttpResponse::Ok().body(format!("Access token content : {content}"))
}
