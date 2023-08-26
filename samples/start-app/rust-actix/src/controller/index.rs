use actix_web::{get, HttpResponse, Responder};

#[tracing::instrument(name = "Index")]
#[get("/")]
async fn index() -> impl Responder {
    HttpResponse::Ok().body("Hello index!")
}
