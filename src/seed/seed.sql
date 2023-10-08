-- Seed data for 'ANIME' category
INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Anime Title ' || generate_series(1, 10),
    'Anime Description ' || generate_series(1, 10),
    '0f57456ef87ea61a.webp',
    '86fa25a6dad7fcc7.mp4',
    'ANIME'
FROM generate_series(1, 10);

-- Seed data for 'DRAMA' category
INSERT INTO catalogs (uuid, title, description, poster, trailer, category)
SELECT
    md5(random()::text || clock_timestamp()::text)::uuid,
    'Drama Title ' || generate_series(1, 10),
    'Drama Description ' || generate_series(1, 10),
    'a9f6e15daf0eca96.webp',
    '86fa25a6dad7fcc7.mp4',
    'DRAMA'
FROM generate_series(1, 10);
