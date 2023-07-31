use actix_web::{get, HttpResponse, Responder};

#[get("/callback")]
async fn callback() -> impl Responder {
    HttpResponse::Ok().body("Hello callback!")
}
