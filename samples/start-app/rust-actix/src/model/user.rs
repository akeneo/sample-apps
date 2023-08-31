use anyhow::{anyhow, Result};
use r2d2::PooledConnection;
use r2d2_sqlite::SqliteConnectionManager;
use serde::{Serialize, Deserialize};

#[derive(Debug, Serialize, Deserialize)]
pub struct User {
    sub: String,
    username: String,
    password: String,
    email: String,
}


pub trait UserRepository {
    fn save(conn:  PooledConnection<SqliteConnectionManager>, user: User) -> Result<()>;
    fn find_by_sub(conn:  PooledConnection<SqliteConnectionManager>, sub: i32) -> Result<User>;
}


impl UserRepository for User {
    fn save(conn:  PooledConnection<SqliteConnectionManager>, user: User) -> Result<()> {
        
        conn.execute(
            "INSERT INTO user (sub, username, password, email) VALUES (?, ?, ?, ?)",
            [user.sub, user.username, user.password, user.email],
        )?;

        Ok(())
    }

    fn find_by_sub(conn:  PooledConnection<SqliteConnectionManager>, sub: i32) -> Result<User> {
        
        let mut stmt = conn.prepare("SELECT * FROM user WHERE sub = ?")?;
        let user_iter = stmt.query_map([sub], |row| {
            Ok(User {
                sub: row.get(0)?,
                username: row.get(1)?,
                password: row.get(2)?,
                email: row.get(3)?,
            })
        })?;

        for user in user_iter {
            return Ok(user?);
        }

        Err(anyhow!("User not found"))
    }
}