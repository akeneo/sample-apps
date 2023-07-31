mod controller;
mod server;

pub use crate::server::run_server;

#[tokio::main]
async fn main() -> std::io::Result<()> {
    run_server()?.await
}
