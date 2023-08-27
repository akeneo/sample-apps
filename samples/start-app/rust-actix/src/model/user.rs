use anyhow::{anyhow, Result};
use rusqlite::Connection;

struct User {
    id: i32,
    username: String,
    password: String,
    email: String,
    sub: String,
}


trait UserRepository {
    fn save(&self, conn: Connection, user: User) -> Result<()>;
    fn find_by_sub(&self, conn: Connection, sub: i32) -> Result<User>;
}


impl UserRepository for User {
    fn save(&self, conn: Connection, user: User) -> Result<()> {
        
        conn.execute(
            "INSERT INTO user (username, password, email, sub) VALUES (?, ?, ?, ?)",
            [user.username, user.password, user.email, user.sub],
        )?;

        Ok(())
    }

    fn find_by_sub(&self, conn: Connection, sub: i32) -> Result<User> {
        
        let mut stmt = conn.prepare("SELECT * FROM user WHERE sub = ?")?;
        let user_iter = stmt.query_map([sub], |row| {
            Ok(User {
                id: row.get(0)?,
                username: row.get(1)?,
                password: row.get(2)?,
                email: row.get(3)?,
                sub: row.get(4)?,
            })
        })?;

        for user in user_iter {
            return Ok(user?);
        }

        Err(anyhow!("User not found"))
    }
}