use actix_web::{get, HttpResponse, Responder};

#[get("/health-check")]
async fn health_check() -> impl Responder {
    HttpResponse::Ok().finish()
}
