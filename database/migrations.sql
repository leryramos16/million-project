-- Migration: Flutter/API conversion support
-- Run once against the `millionproject` database.
-- Safe to re-run: uses IF NOT EXISTS / INSERT IGNORE where possible.

-- 1. Refresh-token storage for JWT auth (mobile clients can't use PHP sessions).
CREATE TABLE IF NOT EXISTS auth_tokens (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    token_hash CHAR(64) NOT NULL COMMENT 'sha256 of the refresh token',
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    UNIQUE KEY token_hash (token_hash),
    CONSTRAINT fk_auth_tokens_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Quest rarity/difficulty, drives card styling (common/rare/epic/legendary look).
ALTER TABLE quests
    ADD COLUMN IF NOT EXISTS difficulty ENUM('easy','medium','hard','legendary') NOT NULL DEFAULT 'easy' AFTER type;

-- 3. Player avatar for the Flutter profile screen.
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) DEFAULT NULL AFTER coins;

-- 4. Seed achievement definitions (table exists but was empty, so unlocks never fired).
INSERT IGNORE INTO achievements (code, title, description, icon, requirement_type, requirement_value, xp_bonus, coins_bonus) VALUES
('first_blood', 'First Blood', 'Complete your very first quest.', 'medal_bronze', 'completed_quests', 1, 25, 10),
('quest_regular', 'Quest Regular', 'Complete 5 quests.', 'medal_silver', 'completed_quests', 5, 50, 25),
('veteran_adventurer', 'Veteran Adventurer', 'Complete 15 quests.', 'medal_gold', 'completed_quests', 15, 100, 50),
('legendary_hero', 'Legendary Hero', 'Complete 30 quests.', 'medal_platinum', 'completed_quests', 30, 250, 100);
