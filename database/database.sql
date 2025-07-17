-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Games table
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    game_cover_url VARCHAR(500),
    release_date DATE,
    developer VARCHAR(255),
    genre VARCHAR(255)
);

-- User's game list table
CREATE TABLE user_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    status ENUM('Playing', 'Completed', 'Dropped', 'Wishlist') NOT NULL,
    personal_rating INT CHECK (personal_rating BETWEEN 0 AND 10),
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

--friendship's table
CREATE TABLE friendships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,

    CONSTRAINT unique_friendship UNIQUE (requester_id, receiver_id)
);

-- rating's table
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rater_id INT NOT NULL,
    rated_game_id INT NOT NULL,
    rating FLOAT NOT NULL CHECK (rating >= 0 AND rating <= 10),
    rate_description VARCHAR(255),

    FOREIGN KEY (rater_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (rated_game_id) REFERENCES games(id) ON DELETE CASCADE,

    UNIQUE (rater_id, rated_game_id)
);

