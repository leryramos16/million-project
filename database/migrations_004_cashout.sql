-- Migration: quest payment amount + coin cashout system.
-- 1 coin = 1 peso, fixed. Coins are deducted from the wallet the moment a
-- cashout is requested (not when paid) so the same coins can't be requested
-- twice; a rejected request refunds them back.

ALTER TABLE quests
    ADD COLUMN IF NOT EXISTS amount_paid INT DEFAULT NULL COMMENT 'Pesos the requester says they paid, entered at submission' AFTER payment_proof;

CREATE TABLE IF NOT EXISTS cashout_requests (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    coins_requested INT(11) NOT NULL,
    peso_amount INT(11) NOT NULL COMMENT 'coins_requested at the 1:1 rate in effect when requested',
    payment_method VARCHAR(50) NOT NULL COMMENT 'gcash, maya, bank_transfer, other',
    account_name VARCHAR(150) NOT NULL,
    account_number VARCHAR(100) NOT NULL,
    status ENUM('pending','paid','rejected') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(255) DEFAULT NULL,
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT fk_cashout_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
