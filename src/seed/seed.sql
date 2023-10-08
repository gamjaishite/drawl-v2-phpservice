INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Anime Title ' || num,
    'Anime Description ' || num,
    '0f57456ef87ea61a.webp',
    '86fa25a6dad7fcc7.mp4',
    'ANIME'
FROM generate_series(1, 100) AS num;

INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Drama Title ' || num,
    'Drama Description ' || num,
    'a9f6e15daf0eca96.webp',
    '86fa25a6dad7fcc7.mp4',
    'DRAMA'
FROM generate_series(1, 100) AS num;
