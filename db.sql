CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    progress INT DEFAULT 0,  -- % hoàn thành
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
