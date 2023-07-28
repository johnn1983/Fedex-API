INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, "2fa_type", username, tagname, email, role_id, phone, description, country, external_link, data, background_image_id, avatar_image_id, password, remember_token, reset_password_hash, created_at, updated_at, email_verified_at, deleted_at, email_verification_token, email_verification_token_sent_at, otp_secret) VALUES
  (1, 'email', 'Gerhard Feest', 'Gerhard Feest', 'admin@example.com', 1, '1111111', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (2, 'email', 'Alien West', 'Alien West', 'user@example.com', 2, '2222222', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null);

INSERT INTO media(name, owner_id, is_public, link, created_at, updated_at, deleted_at) VALUES
  ('Product main photo', 1 , true, 'http://localhost/test.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  ('Category Photo photo', 1, false, 'http://localhost/test1.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  ('Deleted photo', 2, true, 'http://localhost/test3.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  ('Photo', 2, true, 'http://localhost/test4.jpg', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);