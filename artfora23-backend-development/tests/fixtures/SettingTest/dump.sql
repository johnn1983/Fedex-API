INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, "2fa_type", username, tagname, email, role_id, phone, description, country, external_link, data, background_image_id, avatar_image_id, password, remember_token, reset_password_hash, created_at, updated_at, email_verified_at, deleted_at, email_verification_token, email_verification_token_sent_at, otp_secret) VALUES
  (1, 'email', 'Gerhard Feest', 'Gerhard Feest', 'admin@example.com', 1, '1111111', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (2, 'email', 'Alien West', 'Alien West', 'user@example.com', 2, '2222222', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null);

INSERT INTO settings(name, is_public, value, created_at, updated_at) VALUES
  ('attribute', true, '{"name": "color", "value":"#000"}', '2016-10-20 11:05:00', '2016-10-20 11:06:00'),
  ('settings', true, '{"timezone": "australia"}', '2016-10-20 11:06:00', '2016-10-20 11:07:00'),
  ('mailgun', false, '{"api_key": "superKey", "account_id":"3495"}', '2016-10-20 11:07:00', '2016-10-20 11:08:00'),
  ('states', true, '["NSW", "ACT", "NT", "QLD", "SA", "TAS", "VIC", "WA"]', '2016-10-20 11:08:00', '2016-10-20 11:09:00');