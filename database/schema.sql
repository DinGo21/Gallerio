-- Gallerio database schema (MariaDB 11+).
--
-- Run as a MariaDB administrator:
--   sudo mariadb < database/schema.sql

CREATE DATABASE IF NOT EXISTS gallerio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Development credentials: simple and easy to remember, as requested.
CREATE USER IF NOT EXISTS 'gallerio'@'%' IDENTIFIED BY 'gallerio';
GRANT ALL PRIVILEGES ON gallerio.* TO 'gallerio'@'%';
FLUSH PRIVILEGES;

USE gallerio;

CREATE TABLE IF NOT EXISTS images (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Uncategorised',
    tags VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_images_filename (filename),
    KEY idx_images_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
