use actix_web::web;
use anyhow::{anyhow, Result};
use async_trait::async_trait;
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;
use serde::{Deserialize, Serialize};

#[derive(Debug, Serialize, Deserialize)]
pub struct Token {
    pub access_token: String,
}

#[async_trait]
pub trait TokenRepository {
    async fn save(pool: &web::Data<Pool<SqliteConnectionManager>>, token: Token) -> Result<()>;
    async fn exists(pool: &web::Data<Pool<SqliteConnectionManager>>) -> Result<()>;
}

#[async_trait]
impl TokenRepository for Token {
    async fn save(pool: &web::Data<Pool<SqliteConnectionManager>>, token: Token) -> Result<()> {
        let conn = pool.get().unwrap();
        let mut stmt = conn.prepare("SELECT * FROM token WHERE access_token = ?")?;
        if stmt.exists([token.access_token.clone()])? {
            return Ok(());
        }

        // Token should not be saved in clear text, but for the sake of simplicity we do it here.
        conn.execute(
            "INSERT INTO token (access_token) VALUES (?)",
            [token.access_token],
        )
        .unwrap();

        Ok(())
    }

    async fn exists(
        pool: &web::Data<Pool<SqliteConnectionManager>>,
    ) -> Result<()> {
        let conn = pool.get().unwrap();
        let mut stmt = conn.prepare("SELECT * FROM token")?;

        if stmt.exists(())? {
            return Ok(());
        }

        Err(anyhow!("No token exists"))
    }
}
