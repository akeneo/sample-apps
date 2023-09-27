use crate::common::TestApp;

#[tokio::test]
async fn returns_success_without_body() {
    let app: TestApp = TestApp::spawn_app().await;
    let client = reqwest::Client::new();

    let response = client
        .get(&format!("{}/health-check", &app.address))
        .send()
        .await
        .expect("Failed to execute request.");

    assert!(response.status().is_success());
    assert_eq!(Some(0), response.content_length());
}
