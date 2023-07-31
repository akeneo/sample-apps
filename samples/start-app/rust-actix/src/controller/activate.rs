use actix_web::{get, HttpResponse, Responder};

#[get("/activate")]
async fn activate() -> impl Responder {
    HttpResponse::Ok().body("Hello activate!")
}
