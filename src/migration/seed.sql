INSERT INTO users (uuid, name, password, email, role, created_at, updated_at)
VALUES
    ('1a2b3c', 'John Doe', 'password123', 'john@example.com', 'BASIC', NOW(), NOW()),
    ('4d5e6f', 'Jane Smith', 'securepwd', 'jane@example.com', 'ADMIN', NOW(), NOW()),
    ('7g8h9i', 'Alice Johnson', 'pass123', 'alice@example.com', 'BASIC', NOW(), NOW());

INSERT INTO sessions (user_id, created_at, updated_at)
VALUES
    (1, NOW(), NOW()),
    (2, NOW(), NOW()),
    (3, NOW(), NOW());

INSERT INTO catalogs (uuid, title, description, poster, trailer, category, created_at, updated_at)
VALUES
    ('abc123', 'Anime Show 1', 'Description 1', 'poster1.jpg', 'trailer1.mp4', 'ANIME', NOW(), NOW()),
    ('def456', 'Drama Series 1', 'Description 2', 'poster2.jpg', 'trailer2.mp4', 'DRAMA', NOW(), NOW()),
    ('ghi789', 'Mixed Content 1', 'Description 3', 'poster3.jpg', 'trailer3.mp4', 'ANIME', NOW(), NOW());

INSERT INTO watchlists (uuid, title, description, category, user_id, like_count, visibility, created_at, updated_at)
VALUES
    ('wxyz123', 'My Watchlist 1', 'Watchlist description 1', 'MIXED', 1, 0, 'PRIVATE', NOW(), NOW()),
    ('lmnop45', 'Favorites 1', 'Favorites description 1', 'ANIME', 2, 0, 'PUBLIC', NOW(), NOW()),
    ('qrstuv67', 'To Watch 1', 'To Watch description 1', 'DRAMA', 3, 0, 'PRIVATE', NOW(), NOW());

INSERT INTO watchlist_catalog (watchlist_id, catalog_id)
VALUES
    (1, 1),
    (1, 2),
    (2, 3);

INSERT INTO watchlist_like (user_id, watchlist_id)
VALUES
    (1, 2),
    (2, 1),
    (3, 3);

INSERT INTO watchlist_save (user_id, watchlist_id)
VALUES
    (2, 1),
    (3, 2),
    (1, 3);

INSERT INTO comments (uuid, content, user_id, created_at)
VALUES
    ('comment1', 'This is a comment.', 1, NOW()),
    ('comment2', 'Another comment here.', 2, NOW()),
    ('comment3', 'A third comment.', 3, NOW());

INSERT INTO watchlist_comment (watchlist_id, comment_id)
VALUES
    (1, 1),
    (2, 2),
    (3, 3);
