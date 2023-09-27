use sailfish::TemplateOnce;

#[derive(TemplateOnce)]
#[template(path = "./access_token.stpl")]
pub struct AccessToken {
    pub sub: String,
    pub email: String,
}

#[derive(TemplateOnce)]
#[template(path = "./no_access_token.stpl")]
pub struct NoAccessToken {}

#[derive(TemplateOnce)]
#[template(path = "./no_access_token.stpl")]
pub struct Error {}
