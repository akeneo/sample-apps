use anyhow::{anyhow, Result};
use rusqlite::Connection;

struct Token {
    id: i32,
    access_token: String,    
}

trait TokenRepository {
    fn save(&self, conn: Connection, token: Token) -> Result<()>;
    fn find_by_access_token(&self, conn: Connection, access_token: &str) -> Result<Token>;
}

impl TokenRepository for Token {
    fn save(&self, conn: Connection, token: Token) -> Result<()> {
        conn.execute(
            "INSERT INTO token (access_token) VALUES (?)",
            [token.access_token],
        ).unwrap();

        Ok(())
    }

    fn find_by_access_token(&self, conn: Connection, access_token: &str) -> Result<Token> {
        let mut stmt = conn.prepare("SELECT * FROM token WHERE access_token = ?").unwrap();
        let token_iter = stmt.query_map([access_token], |row| {
            Ok(Token {
                id: row.get(0)?,
                access_token: row.get(1)?,
            })
        }).unwrap();

        for token in token_iter {
            return Ok(token.unwrap());
        }

        Err(anyhow!("Token not found"))
    }
}