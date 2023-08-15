use actix_session::Session;
use actix_web::{get, web::{self, Redirect}, Responder};
use rand::distributions::{Alphanumeric, DistString};
use serde::Deserialize;

use crate::{usecase::activate_usecase::ActivateResponse, configuration::Settings};


#[derive(Debug, Deserialize)]
pub struct ActivateRequest {
    pub pim_url : String
}

#[get("/activate")]
async fn activate(
    activate_request: web::Query<ActivateRequest>, 
    session: Session,
    data: web::Data<Settings>
) -> impl Responder {
    // create a random state for preventing cross-site request forgery
    let state: String = Alphanumeric.sample_string(&mut rand::thread_rng(), 16);

    // Store in the user session the state and the PIM URL
    let _ = session.insert("oauth2_state",state.clone() );
    let _ = session.insert("pim_url", activate_request.pim_url.clone());
    
    let client_id = data.pim_client_id.clone();

    Redirect::to(
        ActivateResponse::build(activate_request.into_inner(), client_id, state).redirect_uri
    )
}
