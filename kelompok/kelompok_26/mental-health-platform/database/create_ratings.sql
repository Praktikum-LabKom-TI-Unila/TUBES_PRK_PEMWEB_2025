-- Create ratings table to store per-user ratings for konselor
-- Allows averaging and prevents duplicates per user per konselor

CREATE TABLE IF NOT EXISTS ratings (
  rating_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  konselor_id INT NOT NULL,
  session_id INT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_konselor (user_id, konselor_id),
  INDEX idx_konselor (konselor_id),
  INDEX idx_user (user_id),
  CONSTRAINT fk_ratings_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_ratings_konselor FOREIGN KEY (konselor_id) REFERENCES konselor(konselor_id) ON DELETE CASCADE,
  CONSTRAINT fk_ratings_session FOREIGN KEY (session_id) REFERENCES chat_session(session_id) ON DELETE SET NULL
);
