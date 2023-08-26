use rust_actix::{
    controller::activate::ActivateRequest, usecase::activate_usecase::ActivateResponse,
};
use url::Url;

#[test]
fn return_a_redirection_url() {
    let response = ActivateResponse::build(
        ActivateRequest {
            pim_url: Url::parse("https://example.com").expect("Invalid Url"),
        },
        "client_id".to_owned(),
        "state".to_owned(),
    );
    assert_eq!(response.redirect_uri, "https://example.com/connect/apps/v1/authorize?response_type=code&client_id=client_id&scope=openid%20email%20profile%20read_channel_localization%20read_channel_settings&state=state");
}
