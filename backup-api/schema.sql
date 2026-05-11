CREATE DATABASE IF NOT EXISTS vuon_thoi_quen DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vuon_thoi_quen;

CREATE TABLE IF NOT EXISTS user_backups (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uid          VARCHAR(128) NOT NULL,
  email        VARCHAR(255) DEFAULT NULL,
  habits_json  LONGTEXT NOT NULL,
  stats_json   LONGTEXT NOT NULL,
  backed_up_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_uid (uid),
  INDEX idx_time (backed_up_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS habit_logs (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uid        VARCHAR(128) NOT NULL,
  habit_id   VARCHAR(64)  NOT NULL,
  habit_name VARCHAR(200) NOT NULL,
  log_date   DATE NOT NULL,
  mood       VARCHAR(10)  DEFAULT NULL,
  note       TEXT         DEFAULT NULL,
  streak     SMALLINT     DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_uid_habit_date (uid, habit_id, log_date),
  INDEX idx_uid_date (uid, log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
