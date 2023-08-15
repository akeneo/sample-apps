use actix_session::Session;
use actix_web::{get, HttpResponse, Responder, web};
use serde::Deserialize;

use crate::{configuration::Settings, application::callback_usecase::CallbackAuthorizationRequest};

#[derive(Debug, Deserialize)]
pub struct CallbackRequest {
    pub state: String,
    pub code: String,
}

#[get("/callback")]
async fn callback(
    callback_request: web::Query<CallbackRequest>, 
    session: Session,
    data: web::Data<Settings>
) -> impl Responder {
    // We check if the received state is the same as in the session, for security.
    let state = session.get::<String>("state").unwrap().unwrap();
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
            client_id: data.pim_client_id.clone(),
            client_secret: data.pim_client_secret.clone(),
    };

    let content = authorization_request.execute().await;

    println!("Content: {:?}", content);

    HttpResponse::Ok().body("Hello callback!")
}
