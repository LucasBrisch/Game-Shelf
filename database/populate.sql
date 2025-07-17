use gameshelf;

-- Inserindo usuários
INSERT INTO users (full_name, username, email, password_hash)
VALUES 
('User One', 'userone', 'user1@example.com', 'senha123'),
('User Two', 'usertwo', 'user2@example.com', 'senha123'),
('User Three', 'userthree', 'user3@example.com', 'senha123');

-- Inserindo jogos com capas reais
INSERT INTO games (title, description, game_cover_url, release_date, developer, genre)
VALUES 
(
    'The Witcher 3',
    'Open-world RPG with Geralt of Rivia.',
    'https://cdn.cloudflare.steamstatic.com/steam/apps/292030/header.jpg',
    '2015-05-19',
    'CD Projekt Red',
    'RPG'
),
(
    'Celeste',
    'Challenging platformer with deep story.',
    'https://upload.wikimedia.org/wikipedia/en/6/69/Celeste_box_art_final.png',
    '2018-01-25',
    'Matt Makes Games',
    'Platformer'
),
(
    'Hades',
    'Roguelike dungeon crawler inspired by Greek mythology.',
    'https://cdn.akamai.steamstatic.com/steam/apps/1145360/header.jpg',
    '2020-09-17',
    'Supergiant Games',
    'Roguelike'
);

-- Inserindo amizades
INSERT INTO friendships (requester_id, receiver_id, status)
VALUES 
(1, 2, 'accepted'),
(3, 1, 'pending');

-- Inserindo jogos adicionados por usuários
INSERT INTO user_games (user_id, game_id, status, personal_rating)
VALUES 
(1, 1, 'Completed', 10),
(1, 2, 'Playing', 8),
(2, 1, 'Wishlist', NULL),
(2, 3, 'Playing', 7),
(3, 2, 'Dropped', 4);

-- Inserindo avaliações
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description)
VALUES 
(1, 1, 10, 'Absolute masterpiece.'),
(1, 2, 8.5, 'Very good but very hard.'),
(2, 3, 7, 'Fun gameplay and great art.'),
(3, 2, 4, 'Too frustrating for me.');
