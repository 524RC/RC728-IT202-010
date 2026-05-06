CREATE TABLE IF NOT EXISTS UserWatchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    anime_id INT NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, anime_id),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (anime_id) REFERENCES Anime(id)
);