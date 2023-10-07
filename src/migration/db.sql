DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'role') THEN
        CREATE TYPE role AS ENUM ('BASIC', 'ADMIN');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    name VARCHAR(40) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role role DEFAULT 'BASIC' NOT NULL,

    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(16) PRIMARY KEY,
    user_id INT NOT NULL,
    expired TIMESTAMPTZ NOT NULL,

    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'category') THEN
        CREATE TYPE category AS ENUM ('ANIME', 'DRAMA', 'MIXED');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS catalogs (
    id SERIAL PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    poster VARCHAR(255) NOT NULL,
    trailer VARCHAR(255),
    category category NOT NULL,

    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,

    CHECK (category IN ('ANIME', 'DRAMA'))
);

CREATE OR REPLACE FUNCTION updated_at()
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$ 
BEGIN
    NEW.updated_at = NOW();
    return NEW;
END;
$$;

CREATE OR REPLACE TRIGGER t_user_updated_at BEFORE UPDATE 
ON users FOR EACH ROW EXECUTE PROCEDURE updated_at();

CREATE OR REPLACE TRIGGER t_catalog_updated_at BEFORE UPDATE 
ON catalogs FOR EACH ROW EXECUTE PROCEDURE updated_at();

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'visibility') THEN
        CREATE TYPE visibility AS ENUM ('PRIVATE', 'PUBLIC');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS watchlists (
    id SERIAL NOT NULL PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    title VARCHAR(40) NOT NULL,
    description VARCHAR(255),
    category category NOT NULL DEFAULT 'MIXED',
    user_id integer NOT NULL,
    item_count integer DEFAULT 0 NOT NULL,
    like_count integer DEFAULT 0 NOT NULL,
    visibility visibility DEFAULT 'PRIVATE' NOT NULL,

    created_at timestamp DEFAULT now() NOT NULL,
    updated_at timestamp DEFAULT now() NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE OR REPLACE TRIGGER t_watchlist_updated_at BEFORE UPDATE 
ON watchlists FOR EACH ROW EXECUTE PROCEDURE updated_at();

CREATE TABLE IF NOT EXISTS watchlist_items(
    id SERIAL NOT NULL PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL UNIQUE,

    rank INT NOT NULL,
    description VARCHAR(255),
    watchlist_id integer NOT NULL,
    catalog_id integer NOT NULL,

    created_at timestamp DEFAULT now() NOT NULL,
    updated_at timestamp DEFAULT now() NOT NULL,

    FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE,
    FOREIGN KEY (catalog_id) REFERENCES catalogs(id) ON DELETE CASCADE
);

CREATE OR REPLACE TRIGGER t_watchlist_items_updated_at BEFORE UPDATE 
ON watchlists FOR EACH ROW EXECUTE PROCEDURE updated_at();

CREATE OR REPLACE FUNCTION watchlist_item_count() 
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$
BEGIN
    IF (TG_OP = 'INSERT') THEN
        UPDATE watchlists SET item_count = item_count + 1 WHERE id = NEW.watchlist_id;
    ELSIF (TG_OP = 'DELETE') THEN
        UPDATE watchlists SET item_count = item_count - 1 WHERE id = OLD.watchlist_id;
    END IF;
    RETURN NEW;
END;
$$;

CREATE OR REPLACE TRIGGER t_watchlist_item_count AFTER INSERT OR DELETE
ON watchlist_items FOR EACH ROW EXECUTE PROCEDURE watchlist_item_count();

CREATE TABLE IF NOT EXISTS watchlist_like (
    id SERIAL NOT NULL PRIMARY KEY,
    user_id integer NOT NULL,
    watchlist_id integer NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE
);


CREATE OR REPLACE FUNCTION watchlist_like_count() 
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$
BEGIN
    IF (TG_OP = 'INSERT') THEN
        UPDATE watchlists SET like_count = like_count + 1 WHERE id = NEW.watchlist_id;
    ELSIF (TG_OP = 'DELETE') THEN
        UPDATE watchlists SET like_count = like_count - 1 WHERE id = OLD.watchlist_id;
    END IF;
    RETURN NEW;
END;
$$;

CREATE OR REPLACE TRIGGER t_watchlist_like_count AFTER INSERT OR DELETE
ON watchlist_like FOR EACH ROW EXECUTE PROCEDURE watchlist_like_count();

CREATE TABLE IF NOT EXISTS watchlist_save (
    id SERIAL NOT NULL PRIMARY KEY,
    user_id integer NOT NULL,
    watchlist_id integer NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
    FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id SERIAL NOT NULL PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    content VARCHAR(255) NOT NULL,
    user_id integer NOT NULL,
    watchlist_id integer NOT NULL,

    created_at timestamp DEFAULT now() NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(40) NOT NULL UNIQUE,

    created_at timestamp DEFAULT now() NOT NULL
);

CREATE TABLE IF NOT EXISTS watchlist_tag (
    watchlist_id integer NOT NULL,
    tag_id integer NOT NULL,

    PRIMARY KEY (watchlist_id, tag_id),
    FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

INSERT INTO users (uuid, name, password, email, role) VALUES ('72c5a265715fa26c', 'admin', '$2y$10$rAfDHA4M4ftn8K7Wx82wf.fFODD7PCE/t9CVnBwdLnTDBYjnq7ZnO', 'admin@drawl.com', 'ADMIN');
INSERT INTO tags(name) VALUES ('ACTION'),
                              ('ADVENTURE'),
                              ('ANIMALS'),
                              ('BUSINESS'),
                              ('COMEDY'),
                              ('CRIME'),
                              ('DETECTIVE'),
                              ('DOCUMENTARY'),
                              ('DRAMA'),
                              ('FAMILY'),
                              ('FANTASY'),
                              ('FOOD'),
                              ('HISTORICAL'),
                              ('HORROR'),
                              ('LAW'),
                              ('LIFE'),
                              ('MANGA'),
                              ('MEDICAL'),
                              ('MATURE'),
                              ('MYSTERY'),
                              ('MUSIC'),
                              ('MILITARY'),
                              ('MELODRAMA'),
                              ('PSYCHOLOGICAL'),
                              ('ROMANCE'),
                              ('SCHOOL'),
                              ('SCI-FI'),
                              ('SPORTS'),
                              ('SUPERNATURAL'),
                              ('THRILLER'),
                              ('YOUTH')
;