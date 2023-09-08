use actix_session::Session;
use actix_web::{get, http::header, web, HttpResponse, Responder};
use rand::distributions::{Alphanumeric, DistString};
use serde::Deserialize;
use url::Url;

use crate::{configuration::Settings, usecase::activate_usecase::ActivateResponse};

#[derive(Debug, Deserialize)]
pub struct ActivateRequest {
    pub pim_url: Url,
}

#[tracing::instrument(
    name = "Activate", 
    skip(session, data, activate_request),
    fields(pim_url = %activate_request.pim_url)
)]
#[get("/activate")]
async fn activate(
    session: Session,
    data: web::Data<Settings>,
    activate_request: web::Query<ActivateRequest>,
) -> impl Responder {
    // create a random state for preventing cross-site request forgery
    let state: String = Alphanumeric.sample_string(&mut rand::thread_rng(), 16);

    // Store in the user session the state and the PIM URL
    session.renew();
    session.insert("state", state.clone()).unwrap();
    session
        .insert("pim_url", activate_request.pim_url.clone().as_str())
        .unwrap();

    HttpResponse::Found()
        .insert_header((
            header::LOCATION,
            ActivateResponse::build(
                activate_request.into_inner(),
                data.client_id.clone(),
                data.scopes.clone(),
                state,
            )
            .redirect_uri,
        ))
        .finish()
}
