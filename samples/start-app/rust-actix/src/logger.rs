use tracing::{Subscriber, subscriber::set_global_default};
use tracing_log::LogTracer;
use tracing_bunyan_formatter::{BunyanFormattingLayer, JsonStorageLayer};
use tracing_subscriber::{layer::SubscriberExt, EnvFilter, Registry};
use tracing_subscriber::fmt::MakeWriter;

/// Compose multiple layers into a `tracing`'s subscriber.
fn get_subscriber<Sink>(
    name: String,
    env_filter: String,
    sink: Sink,
) -> impl Subscriber + Send + Sync 
     where
        Sink: for<'a> MakeWriter<'a> + Send + Sync + 'static,
{
    let env_filter = EnvFilter::try_from_default_env()
        .unwrap_or(EnvFilter::new(env_filter));
    let formatting_layer: BunyanFormattingLayer<Sink> = BunyanFormattingLayer::new(
        name,
        sink
    );
    Registry::default()
        .with(env_filter)
        .with(JsonStorageLayer)
        .with(formatting_layer)
}

/// Register a subscriber as global default to process span data.
///
/// It should only be called once!
pub fn init_subscriber(
    name: String,
    env_filter: String,
){
    if std::env::var("LOG_SINK").is_ok() {
        let subscriber = get_subscriber(name, env_filter, std::io::sink);
        LogTracer::init().expect("Failed to set logger");
        set_global_default(subscriber).expect("Failed to set subscriber");
    } else {
       let subscriber = get_subscriber(name, env_filter, std::io::stdout);
       LogTracer::init().expect("Failed to set logger");
       set_global_default(subscriber).expect("Failed to set subscriber");
    }
}