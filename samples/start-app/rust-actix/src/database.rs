use anyhow::Result;
use r2d2::Pool;
use r2d2_sqlite::SqliteConnectionManager;

pub fn init_database(name: String) -> Result<Pool<SqliteConnectionManager>>
{
    let manager = SqliteConnectionManager::file(name);
    let pool = Pool::new(manager).unwrap();

    // Create tables if they don't exist 
    create_tables(pool.clone())?;

    Ok(pool)
}

fn create_tables(pool : Pool<SqliteConnectionManager>) -> Result<()>
{
    let conn = pool.get().unwrap();

    conn.execute(
        "CREATE TABLE IF NOT EXISTS token (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                access_token VARCHAR(255) NOT NULL COLLATE \"BINARY\"
            )",
        [],
    )?;

    conn.execute(
        "CREATE TABLE IF NOT EXISTS user (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                email VARCHAR(255) NOT NULL, 
                firstname VARCHAR(255) NOT NULL, 
                lastname VARCHAR(255) NOT NULL, 
                sub VARCHAR(255) NOT NULL
            )",
        [],
    )?;

    conn.execute(
        "CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_8D93D649580282DC ON user (sub)",
        [],
    )?;

    Ok(())
}