INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO media(name, owner_id, is_public, link, created_at, updated_at, deleted_at) VALUES
  ('Product main photo', 1 , true, 'http://localhost/test.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  ('Category Photo photo', 1, false, 'http://localhost/test1.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  ('Deleted photo', 2, true, 'http://localhost/test3.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  ('Photo', 2, true, 'http://localhost/test4.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);

INSERT INTO users(id, "2fa_type", username, tagname, email, product_visibility_level, role_id, phone, description, country, external_link, data, background_image_id, avatar_image_id, password, remember_token, reset_password_hash, created_at, updated_at, email_verified_at, deleted_at, email_verification_token, email_verification_token_sent_at, otp_secret) VALUES
  (1, 'email', 'Admin', 'admin', 'admin@example.com', 0, 1, '1111111', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (2, 'email', 'User owner of some products', 'owner', 'owner@example.com', 0, 2, '2222222', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (3, 'email', 'User without products', 'user', 'user@example.com', 0, 2, '333333', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (4, 'email', 'User without set visibility level', 'user', 'erotic.lover@example.com', 2, 2, '333333', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null);

INSERT INTO categories(id, title, parent_id, created_at, updated_at) VALUES
  (1, 'category without parent and children', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'parent category', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'children #1', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 'children #2', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO products(id, price, user_id, category_id, title, author, slug, description, is_ai_safe, visibility_level, width, height, weight, status, tags, created_at, updated_at) VALUES
  (1, 1000, 1, 3, 'First', 'Picasso', 'first', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Approved', 'First, Picasso, Some, Tags', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 900, 2, 1, 'Second', 'Shishkin', 'second', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Rejected', 'Second, Shishkin, Some, Tags', '2016-10-21 11:05:00', '2016-10-20 11:05:00'),
  (3, 800, 1, 4, 'Third', 'Vrubel', 'third', 'Lorem ipsum', false, 0, 100, 200, 0.4, 'Pending', 'Third, Vrubel, Some, Tags', '2016-10-22 11:05:00', '2016-10-20 11:05:00'),
  (4, 700, 2, 4, 'Fourth', 'Aivazovkiy', 'fourth', 'Lorem ipsum', false, 0, 100, 200, 0.4, 'Approved', 'Fourth, Aivazovkiy, Some, Tags', '2016-10-23 11:05:00', '2016-10-20 11:05:00'),
  (5, 600, 1, 1, 'Fifth', 'Vasnetsov', 'fifth', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Rejected', 'Fifth, Vasnetsov, Some, Tags', '2016-10-24 11:05:00', '2016-10-20 11:05:00'),
  (6, 500, 2, 3, 'Sixth', 'Picasso', 'sixth', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Pending', 'Sixth, Picasso, Some, Tags', '2016-10-25 11:05:00', '2016-10-20 11:05:00'),
  (7, 400, 1, 1, 'Seventh', 'Shishkin', 'seventh', 'Lorem ipsum', false, 0, 100, 200, 0.4, 'Approved', 'Seventh, Shishkin, Some, Tags', '2016-10-26 11:05:00', '2016-10-20 11:05:00'),
  (8, 300, 2, 4, 'Eighth', 'Vrubel', 'eighth', 'Lorem ipsum', false, 0, 100, 200, 0.4, 'Rejected', 'Eighth, Vrubel, Some, Tags', '2016-10-27 11:05:00', '2016-10-20 11:05:00'),
  (9, 200, 1, 4, 'Ninth', 'Aivazovkiy', 'ninth', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Pending', 'Ninth, Aivazovkiy, Some, Tags', '2016-10-28 11:05:00', '2016-10-20 11:05:00'),
  (10, 100, 2, 1, 'Tenth', 'Vasnetsov', 'tenth', 'Lorem ipsum', true, 0, 100, 200, 0.4, 'Approved', 'Tenth, Vasnetsov, Some, Tags', '2016-10-29 11:05:00', '2016-10-20 11:05:00'),
  (11, 0, 1, 3, 'Eleventh', 'Picasso', 'eleventh', 'Lorem ipsum', false, 0, 100, 200, 0.4, 'Rejected', 'Eleventh, Picasso, Some, Tags', '2016-10-30 11:05:00', '2016-10-20 11:05:00'),
  (12, 0, 2, 3, 'Twelfth', 'Picasso', 'twelfth', 'Lorem ipsum', false, 1, 100, 200, 0.4, 'Approved', 'Twelfth, Picasso, Some, Tags', '2016-10-30 11:05:00', '2016-10-20 11:05:00'),
  (13, 0, 2, 3, 'Thirteenth', 'Picasso', 'thirteenth', 'Lorem ipsum', false, 2, 100, 200, 0.4, 'Approved', 'Thirteenth, Picasso, Some, Tags', '2016-10-31 11:05:00', '2016-10-20 11:05:00'),
  (14, 0, 2, 3, 'Fourteenth', 'Picasso', 'fourteenth', 'Lorem ipsum', false, 3, 100, 200, 0.4, 'Approved', 'Fourteenth, Picasso, Some, Tags', '2016-11-01 11:05:00', '2016-10-20 11:05:00');

INSERT INTO media_product (product_id, media_id) VALUES
  (1, 1),
  (1, 2),
  (2, 1),
  (3, 1),
  (4, 1);