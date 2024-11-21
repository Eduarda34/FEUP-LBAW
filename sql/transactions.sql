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

DELETE FROM users WHERE id = $id;

END TRANSACTION;