DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'role') THEN
        CREATE TYPE role AS ENUM ('BASIC', 'ADMIN');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role role DEFAULT 'BASIC',

    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS sessions (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    expired TIMESTAMPTZ DEFAULT NOW() + INTERVAL '7 days',

    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'category') THEN
        CREATE TYPE category AS ENUM ('ANIME', 'DRAMA');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS catalogs (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    poster VARCHAR(255) NOT NULL,
    trailer VARCHAR(255),
    category category NOT NULL,

    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION user_updated_at()
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$ 
BEGIN
    NEW.updated_at = NOW();
    return NEW;
END;
$$;

CREATE TRIGGER t_user_updated_at BEFORE UPDATE 
ON users FOR EACH ROW EXECUTE PROCEDURE user_updated_at();