use anyhow::{anyhow, Result};
use async_trait::async_trait;
use r2d2::PooledConnection;
use r2d2_sqlite::SqliteConnectionManager;
use serde::{Deserialize, Serialize};

#[derive(Debug, Serialize, Deserialize, Clone)]
pub struct User {
    pub sub: String,
    pub firstname: String,
    pub lastname: String,
    pub email: String,
}

#[async_trait]
pub trait UserRepository {
    async fn save(conn: PooledConnection<SqliteConnectionManager>, user: User) -> Result<()>;
    async fn find_by_sub(conn: PooledConnection<SqliteConnectionManager>, sub: i32)
        -> Result<User>;
}

#[async_trait]
impl UserRepository for User {
    async fn save(conn: PooledConnection<SqliteConnectionManager>, user: User) -> Result<()> {
        conn.execute(
            "INSERT INTO user (sub, firstname, lastname, email) VALUES (?, ?, ?, ?)",
            [user.sub, user.firstname, user.lastname, user.email],
        )?;

        Ok(())
    }

    async fn find_by_sub(
        conn: PooledConnection<SqliteConnectionManager>,
        sub: i32,
    ) -> Result<User> {
        let mut stmt = conn.prepare("SELECT * FROM user WHERE sub = ?")?;
        let user_iter = stmt.query_map([sub], |row| {
            Ok(User {
                sub: row.get(0)?,
                firstname: row.get(1)?,
                lastname: row.get(2)?,
                email: row.get(3)?,
            })
        })?;

        for user in user_iter {
            return Ok(user?);
        }

        Err(anyhow!("User not found"))
    }
}
