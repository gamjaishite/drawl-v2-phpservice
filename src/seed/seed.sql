INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Anime Title ' || num,
    'Anime Description ' || num,
    '5a5ac4ad0c3a5e7c.webp',
    'a3e992b0d939a896.mp4',
    'ANIME'
FROM generate_series(1, 100) AS num;

INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Drama Title ' || num,
    'Drama Description ' || num,
    '5a6b36907cd0f469.webp',
    'a3e992b0d939a896.mp4',
    'DRAMA'
FROM generate_series(1, 100) AS num;

-- Generate and insert 10,000 user records
INSERT INTO users (uuid, name, password, email, role, created_at, updated_at)
SELECT
    uuid_generate_v4() AS uuid,
    'User' || gs AS name,
    '$2y$10$rAfDHA4M4ftn8K7Wx82wf.fFODD7PCE/t9CVnBwdLnTDBYjnq7ZnO' AS password, -- Generating random password hashes
    'user' || gs || '@example.com' AS email,
    'BASIC' AS role,
    NOW() - interval '1 year' * random() AS created_at,
    NOW() - interval '1 year' * random() AS updated_at
FROM generate_series(1, 10000) gs;