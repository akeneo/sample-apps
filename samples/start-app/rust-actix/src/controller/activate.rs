use actix_session::Session;
use actix_web::{get, web, Responder, HttpResponse};
use rand::distributions::{Alphanumeric, DistString};
use serde::Deserialize;

use crate::{usecase::activate_usecase::ActivateResponse, configuration::Settings};


#[derive(Debug, Deserialize)]
pub struct ActivateRequest {
    pub pim_url : String
}

#[get("/activate")]
async fn activate(
    session: Session,    
    data: web::Data<Settings>,
    activate_request: web::Query<ActivateRequest>,
) -> impl Responder {
    // create a random state for preventing cross-site request forgery
    let state: String = Alphanumeric.sample_string(&mut rand::thread_rng(), 16);

    // Store in the user session the state and the PIM URL
    session.insert("state", state.clone()).unwrap();
    session.insert("pim_url", activate_request.pim_url.clone()).unwrap();
    
    let client_id = data.client_id.clone();

    HttpResponse::Found()
        .append_header(("Location", ActivateResponse::build(activate_request.into_inner(), client_id, state).redirect_uri))
        .finish()
}
