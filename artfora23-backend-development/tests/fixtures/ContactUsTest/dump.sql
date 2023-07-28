INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, "2fa_type", username, tagname, email, product_visibility_level, role_id, phone, description, country, external_link, data, background_image_id, avatar_image_id, password, remember_token, reset_password_hash, created_at, updated_at, email_verified_at, deleted_at, email_verification_token, email_verification_token_sent_at, otp_secret) VALUES
  (1, 'email', 'Admin', 'admin', 'admin@example.com', 0, 1, '1111111', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null);

INSERT INTO settings(name, is_public, value, created_at, updated_at) VALUES
  ('contact_us', false, '{"email": "admin@email.com"}', '2016-10-20 11:05:00', '2016-10-20 11:06:00');