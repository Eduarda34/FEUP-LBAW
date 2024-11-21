-- Populate

INSERT INTO users (username, email, password, reputation, created_time) VALUES 
    ('johndoe', 'johndoe@example.com', 'password123', 10, NOW()),
    ('janedoe', 'janedoe@example.com', 'password456', 20, NOW()),
    ('alice', 'alice@example.com', 'password789', 5, NOW()),
    ('Cristiano', 'cristiano_cr7_ronaldo@goat.pt', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 0, NOW());

INSERT INTO system_managers (sm_id) VALUES (1);

INSERT INTO categories (name) VALUES 
    ('Technology'),
    ('Science'),
    ('Health'),
    ('Entertainment');

INSERT INTO user_category (user_id, category_id) VALUES 
    (1, 1),
    (2, 2),
    (3, 3);

INSERT INTO posts (user_id, title, body, created_time) VALUES 
    (1, 'Latest in AI Technology', 'This article explores the latest advancements in AI...', '2024-11-21 18:31:35.42877'),
    (2, 'Breakthroughs in Quantum Computing', 'Quantum computing is advancing at a rapid pace...', '2024-11-21 18:31:35.42877'),
    (4, 'Health Benefits of a Balanced Diet', 'A balanced diet is crucial for maintaining health...', '2024-11-21 18:31:35.42877');

INSERT INTO post_categories (post_id, category_id) VALUES 
    (1, 1),
    (2, 2),
    (3, 3);

INSERT INTO comments (post_id, user_id, body, created_time) VALUES 
    (1, 2, 'Great article on AI advancements!', '2024-11-21 19:31:35.42877'),
    (1, 3, 'Quantum computing has so much potential!', '2024-11-21 19:32:35.42877'),
    (3, 1, 'Very informative, thanks!', '2024-11-21 19:31:35.42877');

INSERT INTO replies (parent_comment_id, comment_id) VALUES 
    (1, 2);
    
INSERT INTO post_votes (user_id, post_id, is_like, time) VALUES 
    (2, 1, TRUE, NOW()),
    (3, 2, FALSE, NOW());

INSERT INTO comment_votes (user_id, comment_id, is_like, time) VALUES 
    (1, 1, TRUE, NOW()),
    (2, 2, TRUE, NOW());

INSERT INTO user_favorites (user_id, post_id) VALUES 
    (1, 1),
    (2, 3);

INSERT INTO user_report (reporter_id, reported_id, reason, time) VALUES 
    (1, 2, 'Inappropriate behavior', NOW()),
    (3, 1, 'Spam content', NOW());

INSERT INTO post_report (reporter_id, post_id, reason, time) VALUES 
    (2, 1, 'Inaccurate information', NOW());

INSERT INTO comment_report (reporter_id, comment_id, reason, time) VALUES 
    (1, 2, 'Offensive comment', NOW());

INSERT INTO blocked_users (blocked_id, blocked_at, reason, report_id) VALUES 
    (2, NOW(), 'Repeated violations', 1);