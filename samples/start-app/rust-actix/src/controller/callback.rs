use actix_session::Session;
use actix_web::{get, HttpResponse, Responder, web};
use reqwest::StatusCode;
use serde::Deserialize;

use crate::{configuration::Settings, application::callback_usecase::CallbackAuthorizationRequest};

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
) -> impl Responder {
    // We check if the received state is the same as in the session, for security.
    let state = match session.get::<String>("state").unwrap() {
        None => return HttpResponse::BadRequest().body("Invalid Session information, missing state"),
        Some(s) => s,
    };

    if state != callback_request.state {
        return HttpResponse::BadRequest().body("Invalid state");
    }

    let pim_url = match session.get::<String>("pim_url").unwrap() {
        None => return HttpResponse::BadRequest().body("Invalid Session information, missing pim_url"),
        Some(p) => p,
    };

    let authorization_request = CallbackAuthorizationRequest {
            pim_url,
            code: callback_request.code.clone(),
            client_id: data.client_id.clone(),
            client_secret: data.client_secret.clone(),
    };

    let (status_code, content ) = authorization_request.execute().await.unwrap();

    if status_code != StatusCode::OK {
        return HttpResponse::BadRequest().body("Invalid request");
    }

    HttpResponse::Ok().body(format!("Access token content : {content}"))
}
