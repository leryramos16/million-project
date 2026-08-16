-- Migration: admin-managed payment methods (GCash/Maya/bank/etc.), shown to
-- players when they submit a quest so they know where to send payment.
-- Run once against the `millionproject` database.

CREATE TABLE IF NOT EXISTS payment_methods (
    id INT(11) NOT NULL AUTO_INCREMENT,
    method VARCHAR(50) NOT NULL COMMENT 'gcash, maya, bank_transfer, other',
    label VARCHAR(100) NOT NULL COMMENT 'Display name shown to players, e.g. "GCash"',
    account_name VARCHAR(150) NOT NULL,
    account_number VARCHAR(100) NOT NULL,
    qr_code_image VARCHAR(255) DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT(11) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
