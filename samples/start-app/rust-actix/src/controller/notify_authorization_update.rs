use actix_web::{get, HttpResponse, Responder};

#[get("/notify-authorization-update")]
async fn notify_authorization_update() -> impl Responder {
    HttpResponse::Ok().body("Hello notify authorization update!")
}
