use anyhow::{anyhow, Result};
use async_trait::async_trait;
use r2d2::PooledConnection;
use r2d2_sqlite::SqliteConnectionManager;
use serde::{Deserialize, Serialize};

#[derive(Debug, Serialize, Deserialize)]
pub struct Token {
    pub access_token: String,
}

#[async_trait]
pub trait TokenRepository {
    async fn save(conn: PooledConnection<SqliteConnectionManager>, token: Token) -> Result<()>;
    async fn find_by_access_token(
        conn: PooledConnection<SqliteConnectionManager>,
        access_token: &str,
    ) -> Result<Token>;
}

#[async_trait]
impl TokenRepository for Token {
    async fn save(conn: PooledConnection<SqliteConnectionManager>, token: Token) -> Result<()> {
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

    async fn find_by_access_token(
        conn: PooledConnection<SqliteConnectionManager>,
        access_token: &str,
    ) -> Result<Token> {
        let mut stmt = conn
            .prepare("SELECT * FROM token WHERE access_token = ?")
            .unwrap();
        let token_iter = stmt
            .query_map([access_token], |row| {
                Ok(Token {
                    access_token: row.get(0)?,
                })
            })
            .unwrap();

        for token in token_iter {
            return Ok(token.unwrap());
        }

        Err(anyhow!("Token not found"))
    }
}
