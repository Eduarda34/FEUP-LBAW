-- CREATE DATABASE newsnet_db;
DROP SCHEMA IF EXISTS lbaw2484 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw2484;
SET search_path TO lbaw2484;

-- Drop tables if they exist to reset the database
DROP TABLE IF EXISTS blocked_users CASCADE;
DROP TABLE IF EXISTS comment_report CASCADE;
DROP TABLE IF EXISTS post_report CASCADE;
DROP TABLE IF EXISTS user_report CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS post_notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS vote_notification CASCADE;
DROP TABLE IF EXISTS follow_notification CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS user_favorites CASCADE;
DROP TABLE IF EXISTS comment_votes CASCADE;
DROP TABLE IF EXISTS post_votes CASCADE;
DROP TABLE IF EXISTS replies CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS post_categories CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS user_category CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS system_managers CASCADE;
DROP TABLE IF EXISTS users CASCADE;


DROP INDEX IF EXISTS index_posts_user_id;
DROP INDEX IF EXISTS index_comments_post_id;
DROP INDEX IF EXISTS index_posts_created_time;

DROP TRIGGER IF EXISTS posts_search_update ON posts CASCADE;
DROP FUNCTION IF EXISTS posts_search_update CASCADE;
DROP INDEX IF EXISTS search_idx;

DROP TRIGGER IF EXISTS prevent_post_deletion ON posts CASCADE;
DROP FUNCTION IF EXISTS prevent_post_deletion CASCADE;
DROP TRIGGER IF EXISTS prevent_comment_deletion ON comments CASCADE;
DROP FUNCTION IF EXISTS prevent_comment_deletion CASCADE;
DROP TRIGGER IF EXISTS trigger_follow_notification ON follows CASCADE;
DROP FUNCTION IF EXISTS create_follow_notification CASCADE;
DROP TRIGGER IF EXISTS trigger_vote_notification_post ON post_votes CASCADE;
DROP FUNCTION IF EXISTS create_vote_notification_post CASCADE;
DROP TRIGGER IF EXISTS trigger_vote_notification_comment ON comment_votes CASCADE;
DROP FUNCTION IF EXISTS create_vote_notification_comment CASCADE;
DROP TRIGGER IF EXISTS trigger_comment_notification ON comments CASCADE;
DROP FUNCTION IF EXISTS create_comment_notification CASCADE;
DROP TRIGGER IF EXISTS trigger_reply_notification ON replies CASCADE;
DROP FUNCTION IF EXISTS create_reply_notification CASCADE;
DROP TRIGGER IF EXISTS trigger_post_notification ON posts CASCADE;
DROP FUNCTION IF EXISTS create_post_notification CASCADE;
DROP TRIGGER IF EXISTS update_reputation_post_vote ON post_votes CASCADE;
DROP TRIGGER IF EXISTS update_reputation_comment_vote ON comment_votes CASCADE;
DROP FUNCTION IF EXISTS update_user_reputation CASCADE;
DROP TRIGGER IF EXISTS check_comment_date_trigger ON comments CASCADE;
DROP FUNCTION IF EXISTS check_comment_date CASCADE;
DROP TRIGGER IF EXISTS check_reply_date_trigger ON comments CASCADE;
DROP FUNCTION IF EXISTS check_reply_date CASCADE;
DROP TRIGGER IF EXISTS no_self_vote_post_trigger ON post_votes CASCADE;
DROP FUNCTION IF EXISTS no_self_vote_post CASCADE;
DROP TRIGGER IF EXISTS no_self_vote_comment_trigger ON comment_votes CASCADE;
DROP FUNCTION IF EXISTS no_self_vote_comment CASCADE;
DROP TRIGGER IF EXISTS no_self_report_user_trigger ON user_report CASCADE;
DROP FUNCTION IF EXISTS no_self_report_user CASCADE;
DROP TRIGGER IF EXISTS no_self_report_post_trigger ON post_report CASCADE;
DROP FUNCTION IF EXISTS no_self_report_ CASCADE;
DROP TRIGGER IF EXISTS no_self_report_comment_trigger ON comment_report CASCADE;
DROP FUNCTION IF EXISTS no_self_report_comment CASCADE;


-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    reputation INT DEFAULT 0,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW()
);

-- System Managers table
CREATE TABLE system_managers (
    sm_id INT PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE
);

-- Follows table (for following users)
CREATE TABLE follows (
    follower_id INT REFERENCES users(id) ON DELETE CASCADE,
    followed_id INT REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (follower_id, followed_id),
    CONSTRAINT no_self_follow CHECK (follower_id <> followed_id) -- BR07
);

-- Categories table
CREATE TABLE categories (
    category_id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- User follows categories
CREATE TABLE user_category (
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(category_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, category_id)
);

-- Posts table
CREATE TABLE posts (
    post_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    synopsis TEXT NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP,
    CONSTRAINT check_edition_date CHECK (updated_at IS NULL OR updated_at > created_at) -- BR12
);

-- Post categories
CREATE TABLE post_categories (
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(category_id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, category_id)
);

-- Comments table
CREATE TABLE comments (
    comment_id SERIAL PRIMARY KEY,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP,
    CONSTRAINT check_edition_date CHECK (updated_at IS NULL OR updated_at > created_at) -- BR12
);

-- Comment replies table
CREATE TABLE replies (
    parent_comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    PRIMARY KEY (parent_comment_id, comment_id),
    CONSTRAINT check_parent_comment CHECK (parent_comment_id <> comment_id)
);

-- votes table for voting on posts 
CREATE TABLE post_votes (
    vote_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    is_like BOOLEAN NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    CONSTRAINT unique_vote_per_post UNIQUE (user_id, post_id) -- BR14
);

-- votes table for voting on comments
CREATE TABLE comment_votes (
    vote_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    is_like BOOLEAN NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    CONSTRAINT unique_vote_per_comment UNIQUE (user_id, comment_id) -- BR14
);

-- User favorites table
CREATE TABLE user_favorites (
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, post_id)
);

-- Notifications tables
CREATE TABLE notifications (
    notification_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    viewed BOOLEAN DEFAULT FALSE,
    time TIMESTAMP DEFAULT NOW()
);

CREATE TABLE follow_notification (
    notification_id INT PRIMARY KEY REFERENCES notifications(notification_id) ON DELETE CASCADE,
    follower_id INT REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE vote_notification (
    notification_id INT PRIMARY KEY REFERENCES notifications(notification_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE
);

CREATE TABLE comment_notification (
    notification_id INT PRIMARY KEY REFERENCES notifications(notification_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    parent_comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE
);

CREATE TABLE post_notification (
    notification_id INT PRIMARY KEY REFERENCES notifications(notification_id) ON DELETE CASCADE,
    author_id INT REFERENCES users(id) ON DELETE SET NULL,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE
);

-- Reports tables
CREATE TABLE reports (
    report_id SERIAL PRIMARY KEY,
    reporter_id INT REFERENCES users(id) ON DELETE CASCADE,
    reason TEXT NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    resolved_time TIMESTAMP,
    CONSTRAINT check_report_date CHECK (resolved_time IS NULL OR resolved_time >= time)
);

CREATE TABLE user_report (
    report_id INT PRIMARY KEY REFERENCES reports(report_id) ON DELETE CASCADE,
    reported_id INT REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE post_report (
    report_id INT PRIMARY KEY REFERENCES reports(report_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE SET NULL
);

CREATE TABLE comment_report (
    report_id INT PRIMARY KEY REFERENCES reports(report_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE SET NULL
);

-- Blocked Users table with reason and reference to report leading to block
CREATE TABLE blocked_users (
    blocked_id INT PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    blocked_at TIMESTAMP DEFAULT NOW(),
    reason TEXT,
    report_id INT REFERENCES reports(report_id) ON DELETE SET NULL
);



-- Indexes

CREATE INDEX index_posts_user_id ON posts USING hash(user_id);

CREATE INDEX index_comments_post_id ON comments USING hash(post_id);

CREATE INDEX index_posts_created_time ON posts USING btree(created_at);
CLUSTER posts USING index_posts_created_time;

-- Full-text Search Indexes

ALTER TABLE posts
ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION posts_search_update()
RETURNS TRIGGER AS 
$BODY$
    BEGIN
    IF TG_OP = 'INSERT' THEN
            NEW.tsvectors = (
            setweight(to_tsvector('english', NEW.title), 'A') ||
            setweight(to_tsvector('english', NEW.body), 'B')
            );
    END IF;
    IF TG_OP = 'UPDATE' THEN
            IF (NEW.title <> OLD.title OR NEW.body <> OLD.body) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', NEW.title), 'A') ||
                setweight(to_tsvector('english', NEW.body), 'B')
            );
            END IF;
    END IF;
    RETURN NEW;
    END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER posts_search_update
BEFORE INSERT OR UPDATE ON posts
FOR EACH ROW
EXECUTE PROCEDURE posts_search_update();

CREATE INDEX search_idx ON posts USING GIN (tsvectors);


-- Triggers

-- BR02: A post or comment cannot be deleted by its author if it has votes or comments.
    -- Posts
CREATE FUNCTION prevent_post_deletion()
RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF (SELECT COUNT(*) FROM comments WHERE post_id = OLD.post_id) > 0 OR
        (SELECT COUNT(*) FROM post_votes WHERE post_id = OLD.post_id) > 0 
        THEN
        RAISE EXCEPTION 'Cannot delete content with existing votes or comments.';
        END IF;
        RETURN OLD;
    END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER prevent_post_deletion
BEFORE DELETE ON posts
FOR EACH ROW 
EXECUTE PROCEDURE prevent_post_deletion();

    -- Comments
CREATE FUNCTION prevent_comment_deletion()
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF (SELECT COUNT(*) FROM replies WHERE parent_comment_id = OLD.comment_id) > 0 OR
        (SELECT COUNT(*) FROM comment_votes WHERE comment_id = OLD.comment_id) > 0 
        THEN
        RAISE EXCEPTION 'Cannot delete content with existing votes or comments.';
        END IF;
        RETURN OLD;
    END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER prevent_comment_deletion
BEFORE DELETE ON comments
FOR EACH ROW 
EXECUTE PROCEDURE prevent_comment_deletion();

-- Notification Triggers
    -- Follow Notification Trigger
CREATE FUNCTION create_follow_notification()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        INSERT INTO notifications (user_id)
        VALUES (NEW.followed_id)
        RETURNING notification_id INTO new_notification_id;

        INSERT INTO follow_notification (notification_id, follower_id)
        VALUES (new_notification_id, NEW.follower_id);

        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trigger_follow_notification
AFTER INSERT ON follows
FOR EACH ROW
EXECUTE PROCEDURE create_follow_notification();

    
    -- Post Vote Notification Trigger
CREATE FUNCTION create_vote_notification_post()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        INSERT INTO notifications (user_id)
        VALUES ((SELECT user_id FROM posts WHERE post_id = NEW.post_id))
        RETURNING notification_id INTO new_notification_id;

        INSERT INTO vote_notification (notification_id, post_id)
        VALUES (new_notification_id, NEW.post_id);

        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER trigger_vote_notification_post
AFTER INSERT ON post_votes
FOR EACH ROW
EXECUTE PROCEDURE create_vote_notification_post();


    -- Comment Vote Notification Trigger
CREATE FUNCTION create_vote_notification_comment()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        INSERT INTO notifications (user_id)
        VALUES ((SELECT user_id FROM comments WHERE comment_id = NEW.comment_id))
        RETURNING notification_id INTO new_notification_id;

        INSERT INTO vote_notification (notification_id, comment_id)
        VALUES (new_notification_id, NEW.comment_id);

        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER trigger_vote_notification_comment
AFTER INSERT ON comment_votes
FOR EACH ROW
EXECUTE PROCEDURE create_vote_notification_comment();


    -- Comment Notification Trigger
CREATE FUNCTION create_comment_notification()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        INSERT INTO notifications (user_id)
        VALUES ((SELECT user_id FROM posts WHERE post_id = NEW.post_id))
        RETURNING notification_id INTO new_notification_id;

        INSERT INTO comment_notification (notification_id, post_id, comment_id)
        VALUES (new_notification_id, NEW.post_id, NEW.comment_id);

        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER trigger_comment_notification
AFTER INSERT ON comments
FOR EACH ROW
EXECUTE PROCEDURE create_comment_notification();


    -- Reply Notification Trigger
CREATE FUNCTION create_reply_notification()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        INSERT INTO notifications (user_id)
        VALUES ((SELECT user_id FROM comments WHERE comment_id = NEW.parent_comment_id))
        RETURNING notification_id INTO new_notification_id;

        INSERT INTO comment_notification (notification_id, post_id, parent_comment_id, comment_id)
        VALUES (new_notification_id, (SELECT post_id FROM comments WHERE comment_id = NEW.comment_id), NEW.parent_comment_id, NEW.comment_id);

        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER trigger_reply_notification
AFTER INSERT ON replies
FOR EACH ROW
EXECUTE PROCEDURE create_reply_notification();
            

    -- Post Notification Trigger (for followed users posting new content)
CREATE FUNCTION create_post_notification()
RETURNS TRIGGER AS 
$BODY$
    DECLARE
        new_notification_id INT;
    BEGIN
        FOR new_notification_id IN
            SELECT follower_id
            FROM follows
            WHERE followed_id = NEW.user_id
        LOOP
            INSERT INTO notifications (user_id)
            VALUES (new_notification_id)
            RETURNING notification_id INTO new_notification_id;

            INSERT INTO post_notification (notification_id, author_id, post_id)
            VALUES (new_notification_id, NEW.user_id, NEW.post_id);
        END LOOP;

        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER trigger_post_notification
AFTER INSERT ON posts
FOR EACH ROW
EXECUTE PROCEDURE create_post_notification();

-- Reputation Triggers
CREATE FUNCTION update_user_reputation() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF TG_TABLE_NAME = 'post_votes' THEN
            IF NEW.is_like THEN
                UPDATE users
                SET reputation = reputation + 1
                WHERE id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id);
            ELSE
                UPDATE users
                SET reputation = reputation - 1
                WHERE id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id);
            END IF;
        ELSIF TG_TABLE_NAME = 'comment_votes' THEN
            IF NEW.is_like THEN
                UPDATE users
                SET reputation = reputation + 1
                WHERE id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id);
            ELSE
                UPDATE users
                SET reputation = reputation - 1
                WHERE id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id);
            END IF;
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

-- Trigger for post votes
CREATE TRIGGER update_reputation_post_vote
AFTER INSERT ON post_votes
FOR EACH ROW
EXECUTE PROCEDURE update_user_reputation();

-- Trigger for comment votes
CREATE TRIGGER update_reputation_comment_vote
AFTER INSERT ON comment_votes
FOR EACH ROW
EXECUTE PROCEDURE update_user_reputation();

-- BR05: Every comment date must be after the article date.
CREATE FUNCTION check_comment_date() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NEW.created_at <= (SELECT created_at FROM posts WHERE post_id = NEW.post_id) THEN
            RAISE EXCEPTION 'Comment date must be after the article date';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER check_comment_date_trigger
BEFORE INSERT OR UPDATE ON comments
FOR EACH ROW EXECUTE PROCEDURE check_comment_date();


-- BR05: Every reply date must be after the parent comment date.
CREATE FUNCTION check_reply_date() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF (SELECT created_at FROM comments WHERE comment_id = NEW.comment_id) <= (SELECT created_at FROM comments WHERE comment_id = NEW.parent_comment_id) THEN
            RAISE EXCEPTION 'Reply date must be after the parent comment date';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER check_reply_date_trigger
BEFORE INSERT OR UPDATE ON replies
FOR EACH ROW EXECUTE PROCEDURE check_reply_date();


-- BR04: A user is unable to vote on their own article or comment.
    -- Posts
CREATE FUNCTION no_self_vote_post() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NEW.user_id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id) THEN
            RAISE EXCEPTION 'User cannot vote on their own post';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_vote_post_trigger
BEFORE INSERT OR UPDATE ON post_votes
FOR EACH ROW EXECUTE PROCEDURE no_self_vote_post();

    -- Comments
CREATE FUNCTION no_self_vote_comment() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NEW.user_id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id) THEN
            RAISE EXCEPTION 'User cannot vote on their own comment';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_vote_comment_trigger
BEFORE INSERT OR UPDATE ON comment_votes
FOR EACH ROW EXECUTE PROCEDURE no_self_vote_comment();
  

-- BR06: A user is unable to report themselves or their own content.
    -- Users
CREATE FUNCTION no_self_report_user() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM reports WHERE report_id = NEW.report_id) THEN
            RAISE EXCEPTION 'Invalid report_id: The report does not exist in the reports table.';
        END IF;
        IF (SELECT reporter_id FROM reports WHERE report_id = NEW.report_id) = NEW.reported_id THEN
            DELETE FROM reports WHERE report_id = NEW.report_id;
            RAISE EXCEPTION 'User cannot report himself';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_report_user_trigger
BEFORE INSERT OR UPDATE ON user_report
FOR EACH ROW EXECUTE PROCEDURE no_self_report_user();

    -- Posts
CREATE FUNCTION no_self_report_post() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM reports WHERE report_id = NEW.report_id) THEN
            RAISE EXCEPTION 'Invalid report_id: The report does not exist in the reports table.';
        END IF;
        IF (SELECT reporter_id FROM reports WHERE report_id = NEW.report_id) = (SELECT user_id FROM posts WHERE post_id = NEW.post_id) THEN
            DELETE FROM reports WHERE report_id = NEW.report_id;
            RAISE EXCEPTION 'User cannot report their own post';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_report_post_trigger
BEFORE INSERT OR UPDATE ON post_report
FOR EACH ROW EXECUTE PROCEDURE no_self_report_post();

    -- Comments
CREATE FUNCTION no_self_report_comment() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM reports WHERE report_id = NEW.report_id) THEN
            RAISE EXCEPTION 'Invalid report_id: The report does not exist in the reports table.';
        END IF;
        IF (SELECT reporter_id FROM reports WHERE report_id = NEW.report_id) = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id) THEN
            DELETE FROM reports WHERE report_id = NEW.report_id;
            RAISE EXCEPTION 'User cannot report their own comment';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_report_comment_trigger
BEFORE INSERT OR UPDATE ON comment_report
FOR EACH ROW EXECUTE PROCEDURE no_self_report_comment();


/* -- Transactions

    -- Create Post
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO posts (user_id, title, body, created_at)
VALUES ($user_id, $title, $body, NOW());

INSERT INTO post_categories (post_id, category_id)
VALUES (currval('posts_post_id_seq'), $category_id);

END TRANSACTION;

    -- Get User Follow Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM follow_notification
WHERE user_id = $user_id
ORDER BY created_at DESC;

END TRANSACTION;

    -- Get User Vote Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM vote_notification
WHERE user_id = $user_id
ORDER BY created_at DESC;

END TRANSACTION;

    -- Get User Comment Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM comment_notification
WHERE user_id = $user_id
ORDER BY created_at DESC;

END TRANSACTION;

    -- Get User Post Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM post_notification
WHERE user_id = $user_id
ORDER BY created_at DESC;

END TRANSACTION;

    -- Get Post Comments
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT *
FROM comments 
WHERE post_id = $post_id 
ORDER BY created_at ASC;

END TRANSACTION;

    -- Create Reply
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO comments (post_id, user_id, body, created_at)
VALUES ($post_id, $user_id, $body, NOW());

INSERT INTO replies (parent_comment_id, comment_id)
VALUES ($parent_comment_id, currval('comments_comment_id_seq'));

END TRANSACTION;

    -- Get Comment Replies
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT *
FROM replies 
WHERE parent_comment_id = $parent_comment_id 
ORDER BY created_at ASC;

END TRANSACTION;

    -- Create Vote On Post
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO post_votes (user_id, post_id, is_like, time)
VALUES ($user_id, $post_id, $is_like, NOW())
ON CONFLICT (user_id, post_id)
DO UPDATE SET is_like = EXCLUDED.is_like, time = NOW();

END TRANSACTION;

    -- Mark Follow Notification As Viewed
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE follow_notification
SET viewed = TRUE
WHERE notification_id = $notification_id AND user_id = $user_id;

END TRANSACTION;

    -- Mark Vote Notification As Viewed
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE vote_notification
SET viewed = TRUE
WHERE notification_id = $notification_id AND user_id = $user_id;

END TRANSACTION;

    -- Mark Comment Notification As Viewed
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE comment_notification
SET viewed = TRUE
WHERE notification_id = $notification_id AND user_id = $user_id;

END TRANSACTION;

    -- Mark Post Notification As Viewed
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE post_notification
SET viewed = TRUE
WHERE notification_id = $notification_id AND user_id = $user_id;

END TRANSACTION;

    -- Delete User
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

DELETE FROM users WHERE id = $id;

END TRANSACTION; */


-- DROP DATABASE IF EXISTS newsnet_db;


-- Populate

INSERT INTO users (username, email, password, reputation, created_at) VALUES 
    ('admin', 'admin@lbaw2484.com', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 0, NOW()),
    ('johndoe', 'johndoe@example.com', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 10, NOW()),
    ('janedoe', 'janedoe@example.com', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 20, NOW()),
    ('alice', 'alice@example.com', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 5, NOW()),
    ('Cristiano', 'cristiano_cr7_ronaldo@goat.pt', '$2y$10$hS16qSDuvdhQvpKyNqmGOOgCtNJ3t7pQwijhQvUAgSzNb7BhegE7C', 0, NOW());

INSERT INTO system_managers (sm_id) VALUES (1);

INSERT INTO categories (name) VALUES 
    ('Technology'),
    ('Science'),
    ('Health'),
    ('Entertainment');

INSERT INTO user_category (user_id, category_id) VALUES 
    (2, 1),
    (2, 2),
    (3, 3);

INSERT INTO posts (user_id, title, synopsis, body, created_at) VALUES 
    (5, 'Latest in AI Technology',
        'AI continues to reshape industries, with breakthroughs in generative content, robotics, and daily life applications. Highlights include advancements in AI-driven creativity, general-purpose robotics, and transformative consumer technologies.',
        'The integration of generative AI into media production has reached new heights. Tools like Runway’s latest video models are producing near-cinematic quality clips in seconds. Major film studios, including Paramount, are exploring generative AI for multilingual lip-syncing and realistic visual effects, signaling a shift in filmmaking techniques. Meanwhile, AI-driven platforms like Synthesia are enabling corporations to generate hyper-realistic avatars, used for everything from marketing to training materials. Inspired by the success of multimodal models like OpenAI’s GPT-4, roboticists are building general-purpose robots capable of handling diverse tasks. These AI systems can perform anything from flipping pancakes to opening doors, with applications ranging from household assistance to industrial automation. Models like DeepMind’s “Robocat” exemplify this progress, merging data self-generation with task versatility. 2024 has seen a surge in consumer-friendly AI applications, such as the Yarbo S1 Plus, an AI-powered snow blower, that autonomously clears driveways with precision mapping and obstacle avoidance, the Anura MagicMirror, that uses AI to assess health metrics like blood pressure and stress levels via facial analysis, bringing medical insights into homes, and AI-integrated pillows and mattresses created by companies like DeRucci, that adjust dynamically to improve sleep quality and reduce snoring. As AI becomes more pervasive, ethical dilemmas and challenges in disinformation management loom large. Generative AI tools, while impressive, also risk being weaponized for fake news and election disinformation. Efforts to counteract these threats, such as watermarking systems like DeepMind’s SynthID, remain in their infancy. The year ahead promises further integration of AI into daily life, but as these innovations expand, ensuring responsible deployment will be a critical focus for researchers and developers.',
        '2024-11-21 18:31:35.42877'),
    (2, 'Breakthroughs in Quantum Computing',
        'Quantum computing is edging closer to practical applications with groundbreaking advancements in hardware, algorithms, and error correction. These developments are setting the stage for solving problems that were once deemed impossible for classical computers.',
        'In a significant leap, IBM unveiled its latest quantum processor, "Condor Q," featuring 1,121 qubits. This processor is not just a demonstration of scaling but a step towards achieving quantum advantage—where quantum computers outperform classical counterparts in meaningful tasks. The chip boasts increased stability and improved connectivity between qubits, which are vital for tackling complex computations such as molecular simulations and optimization problems. IBM’s roadmap also hints at systems exceeding 10,000 qubits within the decade. Meanwhile, Google’s Quantum AI lab showcased "Sycamore 2," capable of simulating chemical reactions with unparalleled accuracy. These simulations promise to revolutionize drug discovery and material science, offering real-world applications in medicine and green energy. One of the most significant barriers to scalable quantum computing—error correction—is being tackled aggressively. Researchers at MIT recently demonstrated a fault-tolerant qubit design that dramatically reduces error rates. This approach, leveraging "logical qubits" built from multiple physical qubits, ensures computational reliability over extended periods. Experts believe this breakthrough brings us closer to practical quantum cryptography and secure communications. Similarly, Microsoft’s topological qubit research made headlines. Their system uses a unique approach to qubit stability, potentially eliminating the need for complex error correction mechanisms. If successful, this innovation could drastically simplify quantum computing architectures. Quantum computing is no longer confined to academic research. Startups like Rigetti Computing and D-Wave Systems are partnering with industries to solve optimization problems in logistics, finance, and energy. For example, Volkswagen is using quantum algorithms to optimize traffic flow in major cities, while JP Morgan Chase explores quantum solutions for risk analysis and portfolio optimization. Furthermore, governments are taking notice. The European Union, the United States, and China have all announced multi-billion-dollar quantum initiatives to remain competitive in the global race for quantum supremacy. Despite these breakthroughs, challenges remain. Quantum systems still require extremely low temperatures to operate, and scaling hardware to accommodate thousands of qubits remains a hurdle. Nonetheless, the pace of innovation and investment in this field suggests a future where quantum computing will unlock solutions to humanity’s most complex challenges. With every milestone, quantum computing edges closer to transforming industries and reshaping our understanding of computation. The next decade will undoubtedly be pivotal in determining the technology’s ultimate impact.',
        '2024-11-21 18:31:35.42877'),
    (4, 'Health Benefits of a Balanced Diet',
        'A balanced diet is the cornerstone of good health, offering numerous benefits from improved immunity to enhanced mental clarity. By incorporating a variety of nutrient-rich foods, individuals can achieve long-term well-being and prevent chronic diseases.',
        'Eating a balanced diet provides the essential nutrients your body needs to function optimally. This includes macronutrients like proteins, carbohydrates, and fats, as well as vital micronutrients such as vitamins and minerals. Consuming these nutrients in appropriate proportions strengthens the immune system, supports cell regeneration, and fuels energy production. For example, whole grains and lean proteins are key for sustained energy, while fruits and vegetables supply antioxidants that combat oxidative stress, reducing the risk of diseases like cancer and heart conditions. What you eat can significantly impact your mental health. Research highlights the role of omega-3 fatty acids, found in fish, nuts, and seeds, in reducing symptoms of depression and anxiety. Similarly, diets rich in B vitamins—available in leafy greens, eggs, and legumes—help regulate brain function and prevent mood swings. The Mediterranean diet, celebrated for its balance of healthy fats, fiber, and fresh produce, has been linked to lower rates of cognitive decline and improved memory, emphasizing the mind-body connection fostered by balanced eating. A diet high in processed foods and refined sugars can significantly increase the risk of obesity, diabetes, and cardiovascular diseases. Conversely, adopting a balanced diet filled with fiber, healthy fats, and lean proteins can help reduce these risks. For heart health, incorporating foods rich in monounsaturated fats, such as avocados, salmon, and olive oil, can improve cholesterol levels and lower the likelihood of developing heart disease. Similarly, for managing diabetes, consuming whole grains and low-glycemic fruits like berries helps stabilize blood sugar levels, reducing spikes that may lead to insulin resistance. Maintaining a healthy weight is easier with a balanced diet. Fiber-rich foods like oats, beans, and vegetables promote satiety, preventing overeating. Additionally, balanced meals provide consistent energy levels, avoiding the sugar crashes associated with high-calorie, low-nutrient foods. To maintain a balanced diet, it’s important to incorporate variety by including a range of colorful fruits and vegetables, ensuring you get a broad spectrum of essential nutrients. Paying attention to portion sizes can help prevent overeating and support healthy weight management. Prioritizing whole, unprocessed foods over highly processed options reduces your intake of added sugars and unhealthy fats. Staying hydrated by drinking plenty of water is also crucial, as it supports digestion, nutrient absorption, and overall body function. Together, these strategies contribute to a sustainable and healthful eating pattern. The benefits of a balanced diet extend beyond physical well-being. By embracing healthy eating habits, individuals can enjoy a higher quality of life, better productivity, and reduced healthcare costs. Achieving balance doesn’t require drastic measures — it’s about making sustainable, thoughtful choices that prioritize health. A balanced diet isn’t just about food; it’s about nourishing your body and mind for a vibrant, energized life.',
        '2024-11-21 18:31:35.42877');

INSERT INTO post_categories (post_id, category_id) VALUES 
    (1, 1),
    (2, 2),
    (3, 3);

INSERT INTO comments (post_id, user_id, body, created_at) VALUES 
    (1, 4, 'Great article on AI advancements!', '2024-11-21 19:31:35.42877'),
    (1, 3, 'Quantum computing has so much potential!', '2024-11-21 19:32:35.42877'),
    (3, 2, 'Very informative, thanks!', '2024-11-21 19:31:35.42877');

INSERT INTO replies (parent_comment_id, comment_id) VALUES 
    (1, 2);
    
INSERT INTO post_votes (user_id, post_id, is_like, time) VALUES 
    (2, 1, TRUE, NOW()),
    (3, 2, FALSE, NOW());

INSERT INTO comment_votes (user_id, comment_id, is_like, time) VALUES 
    (5, 1, TRUE, NOW()),
    (2, 2, TRUE, NOW());

INSERT INTO user_favorites (user_id, post_id) VALUES 
    (5, 1),
    (2, 3);

INSERT INTO reports (reporter_id, reason, time, resolved_time) VALUES 
    (5, 'Inappropriate behavior', NOW(), NOW()),
    (3, 'Spam content', NOW(), NULL),
    (2, 'Inaccurate information', NOW(), NULL),
    (5, 'Offensive comment', NOW(), NULL);

INSERT INTO user_report (report_id, reported_id) VALUES 
    (1, 2),
    (2, 5);

INSERT INTO post_report (report_id, post_id) VALUES 
    (3, 1);

INSERT INTO comment_report (report_id, comment_id) VALUES 
    (4, 2);

INSERT INTO blocked_users (blocked_id, blocked_at, reason, report_id) VALUES 
    (2, NOW(), 'Repeated violations', 1);