CREATE TABLE IF NOT EXISTS user (
    id INTEGER PRIMARY KEY,
    userName TEXT UNIQUE,
    hash TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS container (
  id INTEGER PRIMARY KEY,
  data TEXT NOT NULL,
  passwordSalt TEXT NOT NULL,
  initializationVector TEXT NOT NULL,
  cipher TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS bookmark (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    url TEXT UNIQUE,
    date INTEGER
);

INSERT INTO bookmark (name, url) VALUES ('Cacomania', 'http://cacodaemon.de/');

CREATE TABLE IF NOT EXISTS feed (
  id INTEGER PRIMARY KEY,
  title TEXT NOT NULL,
  url TEXT UNIQUE,
  updated INTEGER,
  interval INTEGER
);

INSERT INTO feed (title, url, updated, interval) VALUES ('Cacomania', 'http://cacodaemon.de/index.php?atom=1', 0, 86400);

CREATE TABLE IF NOT EXISTS item (
  id INTEGER PRIMARY KEY,
  id_feed INTEGER NOT NULL,
  uuid TEXT UNIQUE,
  title TEXT NOT NULL,
  author TEXT,
  content TEXT,
  url TEXT NOT NULL,
  date INTEGER NOT NULL,
  read INTEGER,
  FOREIGN KEY (id_feed) REFERENCES feed(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS fk_item_id_feed ON item (id_feed);

CREATE TABLE IF NOT EXISTS itemqueue (
  id INTEGER PRIMARY KEY,
  id_item INTEGER UNIQUE,
  inserted INTEGER NOT NULL,
  FOREIGN KEY (id_item) REFERENCES item(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS fk_itemqueue_id_item ON itemqueue (id_item);

CREATE TABLE IF NOT EXISTS config (
  id INTEGER PRIMARY KEY,
  key TEXT UNIQUE,
  value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS mailaccount (
  id INTEGER PRIMARY KEY,
  data TEXT NOT NULL,
  passwordSalt TEXT NOT NULL,
  initializationVector TEXT NOT NULL,
  cipher TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS icon (
  id INTEGER PRIMARY KEY,
  inserted INTEGER NOT NULL,
  id_feed INTEGER UNIQUE,
  id_bookmark INTEGER UNIQUE,
  data BLOB NOT NULL,
  FOREIGN KEY (id_feed) REFERENCES feed(id) ON DELETE CASCADE
  FOREIGN KEY (id_bookmark) REFERENCES bookmark(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS fk_icon_id_feed ON icon (id_feed);
CREATE INDEX IF NOT EXISTS fk_icon_id_bookmark ON icon (id_bookmark);

INSERT INTO config (key, value) VALUES ('auto-cleanup-enabled', 'true');
INSERT INTO config (key, value) VALUES ('auto-cleanup-days', 7);
INSERT INTO config (key, value) VALUES ('auto-cleanup-min-item-count', 250);
INSERT INTO config (key, value) VALUES ('auto-cleanup-only-read', 'false');
INSERT INTO config (key, value) VALUES ('auto-cleanup-max-item-count', 1000);
INSERT INTO config (key, value) VALUES ('auto-cleanup-max-item-count-enabled', 'true');

INSERT INTO config (key, value) VALUES ('update-interval-min', 600);
INSERT INTO config (key, value) VALUES ('update-interval-max', 604800);

INSERT INTO config (key, value) VALUES ('database-version', 3);
INSERT INTO config (key, value) VALUES ('api-url', 'http://localhost:8000/api');