use actix_web::{get, HttpResponse, Responder};

#[get("/first-api-call")]
async fn first_api_call() -> impl Responder {
    HttpResponse::Ok().body("Hello first api call!")
}
