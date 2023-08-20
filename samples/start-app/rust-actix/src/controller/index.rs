use actix_session::Session;
use actix_web::{get, HttpResponse, Responder};

#[get("/")]
async fn index(
    session: Session,
) -> impl Responder {
    session.insert("state", "state".to_string()).unwrap();

    HttpResponse::Ok().body("Hello index!")
}
