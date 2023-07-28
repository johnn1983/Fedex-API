INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, "2fa_type", username, tagname, email, role_id, phone, description, country, external_link, data, background_image_id, avatar_image_id, password, remember_token, reset_password_hash, created_at, updated_at, email_verified_at, deleted_at, email_verification_token, email_verification_token_sent_at, otp_secret) VALUES
  (1, 'email', 'Gerhard Feest', 'Gerhard Feest', 'admin@example.com', 1, '1111111', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (2, 'email', 'Alien West', 'Alien West', 'user@example.com', 2, '2222222', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, 'old_password', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (3, 'email', 'Not verified user', 'Not verified user', 'not.verified@email.com', 2, null, 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, 'restore_token', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, 'correct_confirmation_code', '2018-11-11 11:10:11', null),
  (4, 'sms', 'User with SMS 2fa', 'User with SMS 2fa', 'user.sms.2fa@email.com', 2, '3333333', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, 'old_confirmation_code', '2018-11-10 10:10:10', null),
  (5, 'otp', 'User with OTP 2fa', 'User with OTP 2fa', 'user.otp.2fa@email.com', 2, null, 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, 'secret'),
  (6, 'email', 'Restore password', 'Restore password', 'restore@email.com', 2, null, 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null, null),
  (7, 'email', 'Soft deleted user', 'Soft deleted user', 'soft.deleted.user@email.com', 2, '4444444', 'Lorem ipsum well you know', 'USA', null, '{ "media_filters": {} }'::jsonb, null, null, '$2y$10$JSlPT99kMrhDE815OEQKaezYdlgcB0S0uwiOhNnoonyUMc0yQ7KEm', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null, null, null);

insert into password_resets(email, token, created_at) values
    ('restore@email.com', 'restore_token', '2018-11-11 11:11:11'),
    ('restore@email.com', 'old_token', '2018-11-10 11:11:11');

insert into two_factor_auth_emails(id, email, code, created_at, updated_at) values
    (1, 'user@example.com', '123456', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
    (2, 'user@example.com', '098765', '2018-11-10 11:11:11', '2018-11-10 11:11:11');