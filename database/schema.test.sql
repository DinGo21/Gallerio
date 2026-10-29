-- Test database schema (MariaDB 11+).
--
-- Run once as a MariaDB administrator:
--   sudo mariadb < database/schema.test.sql
--
-- Integration tests connect to gallerio_test and skip when it is unavailable.

CREATE DATABASE IF NOT EXISTS gallerio_test
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON gallerio_test.* TO 'gallerio'@'%';
FLUSH PRIVILEGES;

-- Keep the test table in sync with the production schema.
CREATE TABLE IF NOT EXISTS gallerio_test.images LIKE gallerio.images;
