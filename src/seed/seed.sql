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
