DROP DATABASE IF EXISTS game_store;
CREATE DATABASE game_store;
USE game_store;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =========================
-- USERS TABLE
-- =========================

CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin','support') DEFAULT 'user',
    profile_image VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (user_id),
    UNIQUE KEY unique_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (user_id, username, email, password, role, profile_image) VALUES
(1, 'Admin', 'Admin@gmail.com', '$2y$10$H3XeSZSweMn.02THtSp.S.cvmdiyjAQaThVzc4OajR.mOwWDsFX3y', 'admin', 'Images/1777298542_6522516.png'),
(9, 'user1', 'u12@gmail.com', '$2y$10$gzd7DqfD4kA8WmGsBtgLdOv8fyqTjpyEHJyybMbwOtaMnlF5LnJFa', 'user', ''),
(10, 'user', 'zo04@gmail.com', '$2y$10$ZRDsUwQMQozZEA/D3YKleOnrHFHggzXV.qyqmxFhxA8Ys7im/uatu', 'user', 'https://www.gravatar.com/avatar/55e5b8966162cec60642f9f58c563918c89f9686cc82dd403fd6a5435e714008?s=120&d=identicon&r=pg'),
(11, 'r', 'rooa20166@gmail.com', '$2y$10$TOobw8tO92hGP3NGyrfZ..UmayCyNJn5tx.OEu3pS6WBEyJLyJujS', 'user', NULL),
(12, '123', '14@gmail.com', '$2y$10$BKlwPjew8h0FKrBfTOYQ5uIkuqQcLELawV9UgLl2vLUe87xOhbNnW', 'user', ''),
(13, '1212', '12@gmail.com', '$2y$10$xLLO3fSmMQfwo1T6OBFkI.o9j7IRm/RUV1pE3jx0mQmERuLUwURqK', 'user', '');

-- =========================
-- GAMES TABLE
-- =========================

CREATE TABLE games (
    game_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50) DEFAULT NULL,
    price DECIMAL(6,2) NOT NULL,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO games (game_id, title, genre, price, description, image, discount_percent) VALUES
(1, 'The Last of Us Part I', 'Action Adventure', 69.99, 'A story-driven action adventure game set in a post-apocalyptic world.', 'Images/The Last of Us™ Part I.jpg', 0.00),
(2, 'Hollow Knight', 'Adventure', 29.99, 'A challenging 2D action adventure game with exploration and boss fights.', 'Images/Hollow Knight1.jpg', 50.00),
(3, 'FIFA 25', 'Sports', 50.00, 'A football sports game with teams, matches, and competitive gameplay.', 'Images/FC25.0d642987-9b9f-4a80-9ed9-0f67dffa7d23', 0.00),
(4, 'Call of Duty', 'Shooter', 99.00, 'A first-person shooter game with action missions and combat gameplay.', 'Images/Call_of_Duty_Infinite_Warfare_cover.jpg', 0.00),
(5, 'Minecraft', 'Sandbox', 59.99, 'A creative sandbox game where players can build, explore, and survive.', 'Images/Minecraft_2024_cover_art.png.webp', 48.00),
(6, 'fifa', 'Sports', 200.00, 'Play football matches and tournaments.', 'Images/football.jpg', 0.00),
(8, 'PEAK', 'Adventure', 20.00, 'a new game ', 'Images/1778324891_PEAK.png', 1.00),
(10, 'Need for spead', 'Racing', 30.00, 'Car Racing', 'Images/1778325046_needforspeed.png', 0.00);

-- =========================
-- APPLICATIONS TABLE
-- =========================

CREATE TABLE applications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    cv_link TEXT DEFAULT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- CART TABLE
-- =========================

CREATE TABLE cart (
    cart_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (cart_id),
    KEY user_id (user_id),
    CONSTRAINT cart_user_fk FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO cart (cart_id, user_id) VALUES
(1, 1),
(2, 1),
(6, 9),
(7, 10),
(8, 11),
(9, 12),
(10, 13);

-- =========================
-- CART ITEMS TABLE
-- =========================

CREATE TABLE cart_items (
    cart_item_id INT(11) NOT NULL AUTO_INCREMENT,
    cart_id INT(11) NOT NULL,
    game_id INT(11) NOT NULL,
    PRIMARY KEY (cart_item_id),
    UNIQUE KEY unique_cart_game (cart_id, game_id),
    KEY game_id (game_id),
    CONSTRAINT cart_items_cart_fk FOREIGN KEY (cart_id) REFERENCES cart(cart_id),
    CONSTRAINT cart_items_game_fk FOREIGN KEY (game_id) REFERENCES games(game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO cart_items (cart_item_id, cart_id, game_id) VALUES
(3, 1, 1),
(5, 1, 2),
(4, 2, 1),
(6, 2, 2),
(8, 2, 5),
(16, 6, 4),
(21, 8, 2),
(20, 8, 5);

-- =========================
-- OWNED GAMES TABLE
-- =========================

CREATE TABLE owned_games (
    owned_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    game_id INT(11) NOT NULL,
    purchase_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (owned_id),
    UNIQUE KEY unique_owned_game (user_id, game_id),
    KEY game_id (game_id),
    CONSTRAINT owned_games_user_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT owned_games_game_fk FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO owned_games (owned_id, user_id, game_id, purchase_date) VALUES
(1, 1, 3, '2026-05-06 10:29:54'),
(2, 1, 4, '2026-05-06 10:29:54'),
(3, 1, 5, '2026-05-06 10:29:54');

-- =========================
-- REVIEWS TABLE
-- =========================

CREATE TABLE reviews (
    review_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    game_id INT(11) NOT NULL,
    review_rating INT(11) NOT NULL,
    comment TEXT NOT NULL,
    review_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (review_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO reviews (review_id, user_id, game_id, review_rating, comment, review_date) VALUES
(1, 10, 1, 2, 'good', '2026-05-07 12:44:39'),
(2, 10, 4, 5, 'very good', '2026-05-07 12:45:07'),
(3, 13, 2, 3, 'nice', '2026-05-08 18:31:08'),
(4, 10, 8, 5, 'cool', '2026-05-09 11:48:30');

-- =========================
-- SUGGESTIONS TABLE
-- =========================

CREATE TABLE suggestions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id VARCHAR(50) DEFAULT NULL,
    suggestion_text TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO suggestions (id, user_id, suggestion_text, created_at) VALUES
(12, '10', 'add ', '2026-05-09 15:25:44');

-- =========================
-- SUPPORT MESSAGES TABLE
-- =========================

CREATE TABLE support_messages (
    message_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    subject VARCHAR(100) DEFAULT NULL,
    message TEXT NOT NULL,
    PRIMARY KEY (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO support_messages (message_id, name, email, subject, message) VALUES
(13, 'Zahra m', 'z@gmail.com', 'Refund Request', 'Original: I need a refund for the game | Translated (AR): أحتاج إلى رد المبلغ المدفوع للعبة'),
(14, 'Za m', 'zozom6274@gmail.com', 'Payment Problem', 'Original: ds | Translated (AR): سنة');

-- =========================
-- USER LIBRARY TABLE
-- =========================

CREATE TABLE user_library (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    game_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_game (user_id, game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO user_library (id, user_id, game_id) VALUES
(1, 9, 1),
(2, 9, 2),
(3, 9, 3),
(6, 10, 1),
(10, 10, 2),
(4, 10, 4),
(7, 10, 5),
(13, 10, 6),
(11, 10, 8),
(12, 10, 10),
(8, 13, 2);

-- =========================
-- AUTO_INCREMENT VALUES
-- =========================

ALTER TABLE users AUTO_INCREMENT = 15;
ALTER TABLE games AUTO_INCREMENT = 11;
ALTER TABLE applications AUTO_INCREMENT = 4;
ALTER TABLE cart AUTO_INCREMENT = 11;
ALTER TABLE cart_items AUTO_INCREMENT = 31;
ALTER TABLE owned_games AUTO_INCREMENT = 4;
ALTER TABLE reviews AUTO_INCREMENT = 5;
ALTER TABLE suggestions AUTO_INCREMENT = 13;
ALTER TABLE support_messages AUTO_INCREMENT = 15;
ALTER TABLE user_library AUTO_INCREMENT = 15;