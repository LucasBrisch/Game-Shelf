use gameshelf;

-- Usuários
INSERT INTO users (full_name, username, email, password_hash) VALUES
('User One', 'userone', 'user1@example.com', 'senha123'),
('User Two', 'usertwo', 'user2@example.com', 'senha123'),
('User Three', 'userthree', 'user3@example.com', 'senha123'),
('User Four', 'userfour', 'user4@example.com', 'senha123'),
('User Five', 'userfive', 'user5@example.com', 'senha123'),
('User Six', 'usersix', 'user6@example.com', 'senha123'),
('User Seven', 'userseven', 'user7@example.com', 'senha123'),
('User Eight', 'usereight', 'user8@example.com', 'senha123'),
('User Nine', 'usernine', 'user9@example.com', 'senha123'),
('User Ten', 'userten', 'user10@example.com', 'senha123');

-- Jogos
INSERT INTO games (title, description, game_cover_url, release_date, developer, genre) VALUES
('The Witcher 3: Wild Hunt', 'A fantasy RPG where you play as Geralt, a monster hunter.', 'https://upload.wikimedia.org/wikipedia/en/0/0c/Witcher_3_cover_art.jpg', '2015-05-18', 'CD Projekt Red', 'RPG'),
('Celeste', 'A platformer about climbing a mountain and overcoming challenges.', 'https://upload.wikimedia.org/wikipedia/en/9/9d/Celeste_boxart.jpg', '2018-01-25', 'Matt Makes Games', 'Platformer'),
('Hades', 'A roguelike dungeon crawler where you play as the son of Hades.', 'https://upload.wikimedia.org/wikipedia/en/e/e0/Hades_cover_art.jpg', '2020-09-17', 'Supergiant Games', 'Roguelike'),
('Stardew Valley', 'A relaxing farming simulator where you restore a farm.', 'https://upload.wikimedia.org/wikipedia/en/1/1c/Stardew_Valley_cover_art.jpg', '2016-02-26', 'ConcernedApe', 'Simulation'),
('Hollow Knight', 'An action-adventure metroidvania set in a bug-filled world.', 'https://upload.wikimedia.org/wikipedia/en/3/32/Hollow_Knight_cover.jpg', '2017-02-24', 'Team Cherry', 'Metroidvania');

-- Avaliações - The Witcher 3
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description) VALUES
(1, 1, 10, 'Obra-prima.'),
(2, 1, 9, 'História envolvente.'),
(3, 1, 8.5, 'Boa, mas meio longa.'),
(4, 1, 9.2, 'Narrativa excelente.'),
(5, 1, 8.8, 'Imersivo e detalhado.');

-- Avaliações - Celeste
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description) VALUES
(6, 2, 9.5, 'Desafiador e tocante.'),
(7, 2, 8, 'Pixel art linda.'),
(8, 2, 9, 'História sensível.'),
(9, 2, 7.5, 'Boa trilha sonora.'),
(10, 2, 6, 'Muito difícil pra mim.');

-- Avaliações - Hades
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description) VALUES
(1, 3, 9, 'Viciante!'),
(2, 3, 8.2, 'Estilo único.'),
(4, 3, 9.6, 'Combate rápido e divertido.'),
(6, 3, 9.3, 'Top roguelike.'),
(7, 3, 8.7, 'História criativa.');

-- Avaliações - Stardew Valley
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description) VALUES
(3, 4, 8.5, 'Relaxante e viciante.'),
(5, 4, 9, 'Simplesmente ótimo.'),
(6, 4, 7.8, 'Gostei da variedade.'),
(9, 4, 8.2, 'Muito conteúdo!'),
(10, 4, 9.1, 'Ótimo pra jogar com amigos.');

-- Avaliações - Hollow Knight
INSERT INTO ratings (rater_id, rated_game_id, rating, rate_description) VALUES
(1, 5, 10, 'Melhor metroidvania.'),
(2, 5, 9.3, 'Desafiador e gratificante.'),
(3, 5, 8.7, 'Level design impecável.'),
(8, 5, 7.5, 'Arte incrível.'),
(9, 5, 9, 'Atmosfera única.');
