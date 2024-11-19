--
-- Use a specific schema and set it as default - thingy.
--
/*DROP SCHEMA IF EXISTS thingy CASCADE;
CREATE SCHEMA IF NOT EXISTS thingy;
SET search_path TO thingy;

--
-- Drop any existing tables.
--
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS cards CASCADE;
DROP TABLE IF EXISTS items CASCADE;

--
-- Create tables.
--
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  password VARCHAR NOT NULL,
  remember_token VARCHAR
);

CREATE TABLE cards (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  user_id INTEGER REFERENCES users NOT NULL
);

CREATE TABLE items (
  id SERIAL PRIMARY KEY,
  card_id INTEGER NOT NULL REFERENCES cards ON DELETE CASCADE,
  description VARCHAR NOT NULL,
  done BOOLEAN NOT NULL DEFAULT FALSE
);

--
-- Insert value.
--

INSERT INTO users VALUES (
  DEFAULT,
  'John Doe',
  'admin@example.com',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')

INSERT INTO cards VALUES (DEFAULT, 'Things to do', 1);
INSERT INTO items VALUES (DEFAULT, 1, 'Buy milk');
INSERT INTO items VALUES (DEFAULT, 1, 'Walk the dog', true);

INSERT INTO cards VALUES (DEFAULT, 'Things not to do', 1);
INSERT INTO items VALUES (DEFAULT, 2, 'Break a leg');
INSERT INTO items VALUES (DEFAULT, 2, 'Crash the car');
*/

-- CREATE DATABASE newsnet_db;
DROP SCHEMA IF EXISTS lbaw2484 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw2484;
SET search_path TO lbaw2484;

-- Drop tables if they exist to reset the database
DROP TABLE IF EXISTS blocked_users CASCADE;
DROP TABLE IF EXISTS comment_report CASCADE;
DROP TABLE IF EXISTS post_report CASCADE;
DROP TABLE IF EXISTS user_report CASCADE;
DROP TABLE IF EXISTS post_notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS vote_notification CASCADE;
DROP TABLE IF EXISTS follow_notification CASCADE;
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

/*
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
DROP TRIGGER IF EXISTS no_self_report_post_trigger ON post_report CASCADE;
DROP FUNCTION IF EXISTS no_self_report_post CASCADE;
DROP TRIGGER IF EXISTS no_self_report_comment_trigger ON comment_report CASCADE;
DROP FUNCTION IF EXISTS no_self_report_comment CASCADE;
*/

-- Users table
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    reputation INT DEFAULT 0,
    created_time TIMESTAMP DEFAULT NOW()
);

-- System Managers table
CREATE TABLE system_managers (
    sm_id INT PRIMARY KEY REFERENCES users(user_id) ON DELETE CASCADE
);

-- Follows table (for following users)
CREATE TABLE follows (
    follower_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    followed_id INT REFERENCES users(user_id) ON DELETE CASCADE,
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
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(category_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, category_id)
);

-- Posts table
CREATE TABLE posts (
    post_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_time TIMESTAMP DEFAULT NOW(),
    updated_time TIMESTAMP,
    CONSTRAINT check_edition_date CHECK (updated_time IS NULL OR updated_time > created_time) -- BR12
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
    user_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    body TEXT NOT NULL,
    created_time TIMESTAMP DEFAULT NOW(),
    updated_time TIMESTAMP,
    CONSTRAINT check_edition_date CHECK (updated_time IS NULL OR updated_time > created_time) -- BR12
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
    user_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    is_like BOOLEAN NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    CONSTRAINT unique_vote_per_post UNIQUE (user_id, post_id) -- BR14
);

-- votes table for voting on comments
CREATE TABLE comment_votes (
    vote_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    is_like BOOLEAN NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    CONSTRAINT unique_vote_per_comment UNIQUE (user_id, comment_id) -- BR14
);

-- User favorites table
CREATE TABLE user_favorites (
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, post_id)
);

-- Notifications tables
CREATE TABLE follow_notification(
    notification_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    follower_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    viewed BOOLEAN DEFAULT FALSE,
    time TIMESTAMP DEFAULT NOW()
);

CREATE TABLE vote_notification (
    notification_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    viewed BOOLEAN DEFAULT FALSE,
    time TIMESTAMP DEFAULT NOW()
);

CREATE TABLE comment_notification (
    notification_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    parent_comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE CASCADE,
    viewed BOOLEAN DEFAULT FALSE,
    time TIMESTAMP DEFAULT NOW()
);

CREATE TABLE post_notification (
    notification_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    author_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    post_id INT REFERENCES posts(post_id) ON DELETE CASCADE,
    viewed BOOLEAN DEFAULT FALSE,
    time TIMESTAMP DEFAULT NOW()
);

-- Reports tables
CREATE TABLE user_report (
    report_id SERIAL PRIMARY KEY,
    reporter_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    reported_id INT REFERENCES users(user_id) ON DELETE SET NULL,
    reason TEXT NOT NULL,
    time TIMESTAMP DEFAULT NOW(),
    CONSTRAINT no_self_report_user CHECK (reporter_id <> reported_id)
);

CREATE TABLE post_report (
    report_id SERIAL PRIMARY KEY,
    reporter_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    post_id INT REFERENCES posts(post_id) ON DELETE SET NULL,
    reason TEXT NOT NULL,
    time TIMESTAMP DEFAULT NOW()
);

CREATE TABLE comment_report (
    report_id SERIAL PRIMARY KEY,
    reporter_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    comment_id INT REFERENCES comments(comment_id) ON DELETE SET NULL,
    reason TEXT NOT NULL,
    time TIMESTAMP DEFAULT NOW()
);

-- Blocked Users table with reason and reference to report leading to block
CREATE TABLE blocked_users (
    blocked_id INT PRIMARY KEY REFERENCES users(user_id) ON DELETE CASCADE,
    blocked_at TIMESTAMP DEFAULT NOW(),
    reason TEXT,
    report_id INT REFERENCES user_report(report_id) ON DELETE SET NULL
);


/*
-- Indexes

CREATE INDEX index_posts_user_id ON posts USING hash(user_id);

CREATE INDEX index_comments_post_id ON comments USING hash(post_id);

CREATE INDEX index_posts_created_time ON posts USING btree(created_time);
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
    BEGIN
        INSERT INTO follow_notification (user_id, follower_id, time)
        VALUES (NEW.followed_id, NEW.follower_id, NOW());
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
    BEGIN
        INSERT INTO vote_notification (user_id, post_id, time)
        VALUES ((SELECT user_id FROM posts WHERE post_id = NEW.post_id), NEW.post_id, NOW());
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
    BEGIN
        INSERT INTO vote_notification (user_id, comment_id, time)
        VALUES ((SELECT user_id FROM comments WHERE comment_id = NEW.comment_id), NEW.comment_id, NOW());
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
    BEGIN
        INSERT INTO comment_notification (user_id, post_id, comment_id, time)
        VALUES ((SELECT user_id FROM posts WHERE post_id = NEW.post_id), NEW.post_id, NEW.comment_id, NOW());
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
    BEGIN
        INSERT INTO comment_notification (user_id, post_id, parent_comment_id, comment_id, time)
        VALUES ((SELECT user_id FROM comments WHERE comment_id = NEW.parent_comment_id), (SELECT post_id FROM comments WHERE comment_id = NEW.comment_id), NEW.parent_comment_id, NEW.comment_id, NOW());
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
    BEGIN
        INSERT INTO post_notification (user_id, author_id, post_id, time)
        SELECT follower_id, NEW.user_id, NEW.post_id, NOW()
        FROM follows
        WHERE followed_id = NEW.user_id;
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
                WHERE user_id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id);
            ELSE
                UPDATE users
                SET reputation = reputation - 1
                WHERE user_id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id);
            END IF;
        ELSIF TG_TABLE_NAME = 'comment_votes' THEN
            IF NEW.is_like THEN
                UPDATE users
                SET reputation = reputation + 1
                WHERE user_id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id);
            ELSE
                UPDATE users
                SET reputation = reputation - 1
                WHERE user_id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id);
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
        IF NEW.created_time <= (SELECT created_time FROM posts WHERE post_id = NEW.post_id) THEN
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
        IF (SELECT created_time FROM comments WHERE comment_id = NEW.comment_id) <= (SELECT created_time FROM comments WHERE comment_id = NEW.parent_comment_id) THEN
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
    -- Posts
CREATE FUNCTION no_self_report_post() 
RETURNS TRIGGER AS 
$BODY$
    BEGIN
        IF NEW.reporter_id = (SELECT user_id FROM posts WHERE post_id = NEW.post_id) THEN
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
        IF NEW.reporter_id = (SELECT user_id FROM comments WHERE comment_id = NEW.comment_id) THEN
            RAISE EXCEPTION 'User cannot report their own comment';
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER no_self_report_comment_trigger
BEFORE INSERT OR UPDATE ON comment_report
FOR EACH ROW EXECUTE PROCEDURE no_self_report_comment();


-- Transactions

    -- Create Post
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO posts (user_id, title, body, created_time)
VALUES ($user_id, $title, $body, NOW());

INSERT INTO post_categories (post_id, category_id)
VALUES (currval('posts_post_id_seq'), $category_id);

END TRANSACTION;

    -- Get User Follow Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM follow_notification
WHERE user_id = $user_id
ORDER BY created_time DESC;

END TRANSACTION;

    -- Get User Vote Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM vote_notification
WHERE user_id = $user_id
ORDER BY created_time DESC;

END TRANSACTION;

    -- Get User Comment Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM comment_notification
WHERE user_id = $user_id
ORDER BY created_time DESC;

END TRANSACTION;

    -- Get User Post Notifications
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT * FROM post_notification
WHERE user_id = $user_id
ORDER BY created_time DESC;

END TRANSACTION;

    -- Get Post Comments
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT *
FROM comments 
WHERE post_id = $post_id 
ORDER BY created_time ASC;

END TRANSACTION;

    -- Create Reply
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO comments (post_id, user_id, body, created_time)
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
ORDER BY created_time ASC;

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

DELETE FROM users WHERE user_id = $user_id;

END TRANSACTION;


-- DROP DATABASE IF EXISTS newsnet_db;
*/