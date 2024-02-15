DROP TABLE IF EXISTS subscribers;

CREATE TABLE subscribers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL,
  first_name TEXT DEFAULT NULL,
  last_name TEXT DEFAULT NULL,
  status TEXT DEFAULT NULL CHECK(status IN ('active', 'inactive')),
  UNIQUE (email)
);