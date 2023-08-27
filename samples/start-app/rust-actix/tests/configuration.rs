use rust_actix::configuration::Settings;

#[test]
fn it_should_load_test_env_variables() {
    let settings = Settings::get(Some("tests/features/.env.test".to_string()));

    assert_eq!(settings.app_server_addr, "0.0.0.0:0");
    assert_eq!(settings.app_name, "test");
    assert_eq!(settings.log_level, "debug");
    assert_eq!(settings.client_id, "pim_client_id");
    assert_eq!(settings.client_secret, "pim_client_secret");
    assert_eq!(settings.secure_cookie, false);
}