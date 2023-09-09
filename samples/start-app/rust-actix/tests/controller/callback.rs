use crate::common::TestApp;

#[tokio::test]
async fn returns_a_bad_request_when_state_missing() {
    let app: TestApp = TestApp::spawn_app().await;
    let client = reqwest::Client::new();

    let response = client
        .get(&format!("{}/callback", &app.address))
        .send()
        .await
        .expect("Failed to execute request.");

    assert_eq!(response.status().as_u16(), 400);
}

#[tokio::test]
async fn returns_a_bad_request_when_pim_url_missing() {
    let app: TestApp = TestApp::spawn_app().await;
    let client = reqwest::Client::new();

    let response = client
        .get(&format!("{}/callback", &app.address))
        .send()
        .await
        .expect("Failed to execute request.");

    assert_eq!(response.status().as_u16(), 400);
}
