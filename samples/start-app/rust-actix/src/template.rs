use sailfish::TemplateOnce;

use crate::model::user::User;

#[derive(TemplateOnce)]
#[template(path = "./access_token.stpl")]
pub struct AccessToken {
    pub user: Option<User>,
}

#[derive(TemplateOnce)]
#[template(path = "./no_access_token.stpl")]
pub struct NoAccessToken {
    
}

#[derive(TemplateOnce)]
#[template(path = "./no_access_token.stpl")]
pub struct Error {
    
}