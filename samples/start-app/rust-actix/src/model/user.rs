use actix_web::web;
use anyhow::{anyhow, Result};
use async_trait::async_trait;
use r2d2::Pool;
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
    async fn save(pool: &web::Data<Pool<SqliteConnectionManager>>, user: User) -> Result<()>;
    async fn find_by_sub(
        pool: &web::Data<Pool<SqliteConnectionManager>>,
        sub: &str,
    ) -> Result<User>;
}

#[async_trait]
impl UserRepository for User {
    async fn save(pool: &web::Data<Pool<SqliteConnectionManager>>, user: User) -> Result<()> {
        let conn = pool.get().unwrap();
        let mut stmt = conn.prepare("SELECT * FROM user WHERE sub = ?")?;
        if stmt.exists([user.sub.clone()])? {
            return Ok(());
        }

        conn.execute(
            "INSERT INTO user (sub, firstname, lastname, email) VALUES (?, ?, ?, ?)",
            [user.sub, user.firstname, user.lastname, user.email],
        )?;

        Ok(())
    }

    async fn find_by_sub(
        pool: &web::Data<Pool<SqliteConnectionManager>>,
        sub: &str,
    ) -> Result<User> {
        let conn = pool.get().unwrap();

        let mut stmt =
            conn.prepare("SELECT sub, firstname, lastname, email FROM user WHERE sub = ?")?;
        let mut rows = stmt.query([sub])?;

        if let Some(row) = rows.next()? {
            return Ok(User {
                sub: row.get(0)?,
                firstname: row.get(1)?,
                lastname: row.get(2)?,
                email: row.get(3)?,
            });
        }

        Err(anyhow!("User not found"))
    }
}
