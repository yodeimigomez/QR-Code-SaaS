CREATE TABLE `users` (
`user_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`email` varchar(320) NOT NULL,
`password` varchar(128) DEFAULT NULL,
`name` varchar(64) NOT NULL,
`billing` text,
`api_key` varchar(32) DEFAULT NULL,
`token_code` varchar(32) DEFAULT NULL,
`twofa_secret` varchar(16) DEFAULT NULL,
`anti_phishing_code` varchar(8) DEFAULT NULL,
`one_time_login_code` varchar(32) DEFAULT NULL,
`pending_email` varchar(128) DEFAULT NULL,
`email_activation_code` varchar(32) DEFAULT NULL,
`lost_password_code` varchar(32) DEFAULT NULL,
`type` tinyint NOT NULL DEFAULT '0',
`status` tinyint NOT NULL DEFAULT '0',
`is_newsletter_subscribed` tinyint NOT NULL DEFAULT '0',
`has_pending_internal_notifications` tinyint NOT NULL DEFAULT '0',
`plan_id` varchar(16) NOT NULL DEFAULT '',
`plan_expiration_date` datetime DEFAULT NULL,
`plan_settings` text,
`plan_trial_done` tinyint DEFAULT '0',
`plan_expiry_reminder` tinyint DEFAULT '0',
`payment_subscription_id` varchar(64) DEFAULT NULL,
`payment_processor` varchar(16) DEFAULT NULL,
`payment_total_amount` float DEFAULT NULL,
`payment_currency` varchar(4) DEFAULT NULL,
`referral_key` varchar(32) DEFAULT NULL,
`referred_by` varchar(32) DEFAULT NULL,
`referred_by_has_converted` tinyint DEFAULT '0',
`language` varchar(32) DEFAULT 'english',
`timezone` varchar(32) DEFAULT 'UTC',
`datetime` datetime DEFAULT NULL,
`ip` varchar(64) DEFAULT NULL,
`continent_code` varchar(8) DEFAULT NULL,
`country` varchar(8) DEFAULT NULL,
`city_name` varchar(32) DEFAULT NULL,
`device_type` varchar(16) DEFAULT NULL,
`browser_language` varchar(32) DEFAULT NULL,
`browser_name` varchar(32) DEFAULT NULL,
`os_name` varchar(16) DEFAULT NULL,
`last_activity` datetime DEFAULT NULL,
`total_logins` int DEFAULT '0',
`user_deletion_reminder` tinyint(4) DEFAULT '0',
`source` varchar(32) DEFAULT 'direct',
PRIMARY KEY (`user_id`),
KEY `plan_id` (`plan_id`),
KEY `api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `users` (`user_id`, `email`, `password`, `api_key`, `referral_key`, `name`, `type`, `status`, `plan_id`, `plan_expiration_date`, `plan_settings`, `datetime`, `ip`, `last_activity`)
VALUES (1,'admin','$2y$10$uFNO0pQKEHSFcus1zSFlveiPCB3EvG9ZlES7XKgJFTAl5JbRGFCWy', md5(rand()), md5(rand()), 'AltumCode',1,1,'custom','2030-01-01 12:00:00', '{"qr_codes_limit":-1,"links_limit":-1,"projects_limit":-1,"pixels_limit":-1,"domains_limit":-1,"statistics_retention":365,"additional_domains_is_enabled":true,"analytics_is_enabled":true,"custom_url_is_enabled":true,"password_protection_is_enabled":true,"api_is_enabled":true,"affiliate_is_enabled":false,"no_ads":true,"enabled_qr_codes":{"text":true,"url":true,"phone":true,"sms":true,"email":true,"whatsapp":true,"facetime":true,"location":true,"wifi":true,"event":true,"crypto":true,"vcard":true}}', NOW(),'',NOW());

-- SEPARATOR --

CREATE TABLE `users_logs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`type` varchar(64) DEFAULT NULL,
`ip` varchar(64) DEFAULT NULL,
`device_type` varchar(16) DEFAULT NULL,
`os_name` varchar(16) DEFAULT NULL,
`continent_code` varchar(8) DEFAULT NULL,
`country_code` varchar(8) DEFAULT NULL,
`city_name` varchar(32) DEFAULT NULL,
`browser_language` varchar(32) DEFAULT NULL,
`browser_name` varchar(32) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `users_logs_user_id` (`user_id`),
KEY `users_logs_ip_type_datetime_index` (`ip`,`type`,`datetime`),
CONSTRAINT `users_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `plans` (
`plan_id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(64) NOT NULL DEFAULT '',
`description` varchar(256) NOT NULL DEFAULT '',
`monthly_price` float DEFAULT NULL,
`annual_price` float DEFAULT NULL,
`lifetime_price` float DEFAULT NULL,
`trial_days` int unsigned NOT NULL DEFAULT '0',
`settings` text NOT NULL,
`taxes_ids` text,
`codes_ids` text,
`color` varchar(16) DEFAULT NULL,
`status` tinyint NOT NULL,
`order` int unsigned DEFAULT '0',
`datetime` datetime NOT NULL,
PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `pages_categories` (
`pages_category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`url` varchar(256) NOT NULL,
`title` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) DEFAULT NULL,
`icon` varchar(32) DEFAULT NULL,
`order` int NOT NULL DEFAULT '0',
`language` varchar(32) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`pages_category_id`),
KEY `url` (`url`),
KEY `pages_categories_url_language_index` (`url`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- SEPARATOR --

CREATE TABLE `pages` (
`page_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`pages_category_id` bigint unsigned DEFAULT NULL,
`url` varchar(256) NOT NULL,
`title` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) DEFAULT NULL,
`icon` varchar(32) DEFAULT NULL,
`keywords` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
`editor` varchar(16) DEFAULT NULL,
`content` longtext,
`type` varchar(16) DEFAULT '',
`position` varchar(16) NOT NULL DEFAULT '',
`language` varchar(32) DEFAULT NULL,
`open_in_new_tab` tinyint DEFAULT '1',
`order` int DEFAULT '0',
`total_views` bigint unsigned DEFAULT '0',
`is_published` tinyint DEFAULT '1',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`page_id`),
KEY `pages_pages_category_id_index` (`pages_category_id`),
KEY `pages_url_index` (`url`),
KEY `pages_is_published_index` (`is_published`),
KEY `pages_language_index` (`language`),
CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`pages_category_id`) REFERENCES `pages_categories` (`pages_category_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `pages` (`pages_category_id`, `url`, `title`, `description`, `content`, `type`, `position`, `order`, `total_views`, `datetime`, `last_datetime`) VALUES
(NULL, 'https://altumcode.com/', 'Software by AltumCode', '', '', 'external', 'bottom', 1, 0, NOW(), NOW()),
(NULL, 'https://altumco.de/66qrcode', 'Built with 66qrcode', '', '', 'external', 'bottom', 0, 0, NOW(), NOW());

-- SEPARATOR --

CREATE TABLE `blog_posts_categories` (
`blog_posts_category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`url` varchar(256) NOT NULL,
`title` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) DEFAULT NULL,
`order` int NOT NULL DEFAULT '0',
`language` varchar(32) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`blog_posts_category_id`),
KEY `url` (`url`),
KEY `blog_posts_categories_url_language_index` (`url`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- SEPARATOR --

CREATE TABLE `blog_posts` (
`blog_post_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`blog_posts_category_id` bigint unsigned DEFAULT NULL,
`url` varchar(256) NOT NULL,
`title` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) DEFAULT NULL,
`keywords` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`image` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`editor` varchar(16) DEFAULT NULL,
`content` longtext,
`language` varchar(32) DEFAULT NULL,
`total_views` bigint unsigned DEFAULT '0',
`is_published` tinyint DEFAULT '1',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`blog_post_id`),
KEY `blog_post_id_index` (`blog_post_id`),
KEY `blog_post_url_index` (`url`),
KEY `blog_posts_category_id` (`blog_posts_category_id`),
KEY `blog_posts_is_published_index` (`is_published`),
KEY `blog_posts_language_index` (`language`),
CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`blog_posts_category_id`) REFERENCES `blog_posts_categories` (`blog_posts_category_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `broadcasts` (
`broadcast_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(64) DEFAULT NULL,
`subject` varchar(128) DEFAULT NULL,
`content` text,
`segment` varchar(64) DEFAULT NULL,
`settings` text COLLATE utf8mb4_unicode_ci,
`users_ids` longtext CHARACTER SET utf8mb4,
`sent_users_ids` longtext,
`sent_emails` int unsigned DEFAULT '0',
`total_emails` int unsigned DEFAULT '0',
`status` varchar(16) DEFAULT NULL,
`views` bigint unsigned DEFAULT '0',
`clicks` bigint unsigned DEFAULT '0',
`last_sent_email_datetime` datetime DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`broadcast_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `broadcasts_statistics` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`broadcast_id` bigint unsigned DEFAULT NULL,
`type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`target` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `broadcast_id` (`broadcast_id`),
KEY `broadcasts_statistics_user_id_broadcast_id_type_index` (`broadcast_id`,`user_id`,`type`),
CONSTRAINT `broadcasts_statistics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `broadcasts_statistics_ibfk_2` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcasts` (`broadcast_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `internal_notifications` (
`internal_notification_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`for_who` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`from_who` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_read` tinyint unsigned DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`read_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`internal_notification_id`),
KEY `user_id` (`user_id`),
KEY `users_notifications_for_who_idx` (`for_who`) USING BTREE,
CONSTRAINT `internal_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `settings` (
`id` int NOT NULL AUTO_INCREMENT,
`key` varchar(64) NOT NULL DEFAULT '',
`value` longtext NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

SET @cron_key = MD5(RAND());

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES
('main', '{"title":"Your title","default_language":"english","default_theme_style":"light","default_timezone":"UTC","index_url":"","terms_and_conditions_url":"","privacy_policy_url":"","not_found_url":"","se_indexing":true,"display_index_plans":true,"display_index_testimonials":true,"display_index_faq":true,"default_results_per_page":100,"default_order_type":"DESC","auto_language_detection_is_enabled":true,"blog_is_enabled":false,"api_is_enabled":true,"logo_light":"","logo_dark":"","logo_email":"","opengraph":"","favicon":"","openai_api_key":"","force_https_is_enabled":false,"broadcasts_statistics_is_enabled":true}'),
('languages', '{"english":{"status":"active"}}'),
('users', '{"email_confirmation":false,"welcome_email_is_enabled":false,"register_is_enabled":true,"register_only_social_logins":false,"register_display_newsletter_checkbox":false,"auto_delete_unconfirmed_users":30,"auto_delete_inactive_users":90,"user_deletion_reminder":0,"blacklisted_domains":"","blacklisted_countries":[],"login_lockout_is_enabled":true,"login_lockout_max_retries":3,"login_lockout_time":60,"lost_password_lockout_is_enabled":true,"lost_password_lockout_max_retries":3,"lost_password_lockout_time":60,"resend_activation_lockout_is_enabled":true,"resend_activation_lockout_max_retries":3,"resend_activation_lockout_time":60,"register_lockout_is_enabled":true,"register_lockout_max_registrations":3,"register_lockout_time":10}'),
('ads', '{\"header\":\"\",\"footer\":\"\"}'),
('captcha', '{\"type\":\"basic\",\"recaptcha_public_key\":\"\",\"recaptcha_private_key\":\"\",\"login_is_enabled\":0,\"register_is_enabled\":0,\"lost_password_is_enabled\":0,\"resend_activation_is_enabled\":0}'),
('cron', concat('{\"key\":\"', @cron_key, '\"}')),
('email_notifications', '{"emails":"","new_user":false,"delete_user":false,"new_payment":false,"new_domain":false,"new_affiliate_withdrawal":false,"contact":false}'),
('internal_notifications', '{}'),
('content', '{"blog_is_enabled":true,"blog_share_is_enabled":true,"blog_categories_widget_is_enabled":true,"blog_popular_widget_is_enabled":true,"blog_views_is_enabled":true,"pages_is_enabled":true,"pages_share_is_enabled":true,"pages_popular_widget_is_enabled":true,"pages_views_is_enabled":true}'),
('facebook', '{\"is_enabled\":\"0\",\"app_id\":\"\",\"app_secret\":\"\"}'),
('google', '{\"is_enabled\":\"0\",\"client_id\":\"\",\"client_secret\":\"\"}'),
('twitter', '{\"is_enabled\":\"0\",\"consumer_api_key\":\"\",\"consumer_api_secret\":\"\"}'),
('discord', '{\"is_enabled\":\"0\"}'),
('linkedin', '{\"is_enabled\":\"0\"}'),
('microsoft', '{\"is_enabled\":\"0\"}'),
('plan_custom', '{"plan_id":"custom","name":"Custom","description":"","price":":)","custom_button_url":"https:\/\/example.com\/","color":null,"status":2,"settings":{"qr_codes_limit":-1,"links_limit":-1,"projects_limit":-1,"pixels_limit":-1,"domains_limit":-1,"statistics_retention":-1,"additional_domains_is_enabled":true,"analytics_is_enabled":true,"custom_url_is_enabled":true,"password_protection_is_enabled":true,"api_is_enabled":true,"affiliate_is_enabled":false,"no_ads":true,"enabled_qr_codes":{"text":true,"url":true,"phone":true,"sms":true,"email":true,"whatsapp":true,"facetime":true,"location":true,"wifi":true,"event":true,"crypto":true,"vcard":true}}}'),
('plan_free', '{"plan_id":"free","name":"Free","description":"","price":"Free","color":null,"status":1,"settings":{"qr_codes_limit":5,"links_limit":5,"projects_limit":5,"pixels_limit":5,"domains_limit":5,"statistics_retention":180,"additional_domains_is_enabled":true,"analytics_is_enabled":true,"custom_url_is_enabled":true,"password_protection_is_enabled":true,"api_is_enabled":true,"affiliate_is_enabled":false,"no_ads":true,"enabled_qr_codes":{"text":true,"url":true,"phone":true,"sms":true,"email":true,"whatsapp":true,"facetime":true,"location":true,"wifi":true,"event":true,"crypto":true,"vcard":true}}}'),
('plan_guest', '{"plan_id":"guest","name":"Guest","description":"","price":"Free","color":null,"status":1,"settings":{"qr_codes_limit":0,"projects_limit":0,"pixels_limit":0,"domains_limit":0,"statistics_retention":0,"additional_domains_is_enabled":false,"analytics_is_enabled":false,"custom_url_is_enabled":false,"password_protection_is_enabled":false,"api_is_enabled":false,"affiliate_is_enabled":false,"no_ads":false,"enabled_qr_codes":{"text":true,"url":true,"phone":true,"sms":false,"email":true,"whatsapp":false,"facetime":false,"location":false,"wifi":false,"event":false,"crypto":false,"vcard":false}}}'),
('payment', '{\"is_enabled\":false,\"type\":\"both\",\"currency\":\"USD\",\"codes_is_enabled\":false,\"taxes_and_billing_is_enabled\":false,\"invoice_is_enabled\":false}'),
('paypal', '{\"is_enabled\":\"0\",\"mode\":\"sandbox\",\"client_id\":\"\",\"secret\":\"\"}'),
('stripe', '{\"is_enabled\":\"0\",\"publishable_key\":\"\",\"secret_key\":\"\",\"webhook_secret\":\"\"}'),
('offline_payment', '{\"is_enabled\":\"0\",\"instructions\":\"Your offline payment instructions go here..\"}'),
('coinbase', '{\"is_enabled\":\"0\"}'),
('payu', '{\"is_enabled\":\"0\"}'),
('paystack', '{\"is_enabled\":\"0\"}'),
('razorpay', '{\"is_enabled\":\"0\"}'),
('mollie', '{\"is_enabled\":\"0\"}'),
('yookassa', '{\"is_enabled\":\"0\"}'),
('crypto_com', '{\"is_enabled\":\"0\"}'),
('paddle', '{\"is_enabled\":\"0\"}'),
('mercadopago', '{\"is_enabled\":\"0\"}'),
('smtp', '{\"host\":\"\",\"from\":\"\",\"from_name\":\"\",\"encryption\":\"tls\",\"port\":\"587\",\"auth\":\"1\",\"username\":\"\",\"password\":\"\"}'),
('theme', '{}'),
('custom', '{\"head_js\":\"\",\"head_css\":\"\"}'),
('socials', '{"youtube":"","facebook":"","twitter":"","instagram":"","tiktok":"","linkedin":"","whatsapp":"","email":""}'),
('announcements', '{"guests_id":"16e2fdd0e771da32ec9e557c491fe17d","guests_content":"","guests_text_color":"#ffffff","guests_background_color":"#000000","users_id":"16e2fdd0e771da32ec9e557c491fe17d","users_content":"","users_text_color":"#dbebff","users_background_color":"#000000"}'),
('business', '{\"brand_name\":\"66qrcode\",\"invoice_nr_prefix\":\"INVOICE-\",\"name\":\"\",\"address\":\"\",\"city\":\"\",\"county\":\"\",\"zip\":\"\",\"country\":\"AF\",\"email\":\"\",\"phone\":\"\",\"tax_type\":\"\",\"tax_id\":\"\",\"custom_key_one\":\"\",\"custom_value_one\":\"\",\"custom_key_two\":\"\",\"custom_value_two\":\"\"}'),
('webhooks', '{"user_new":"","user_delete":"","payment_new":"","code_redeemed":"","contact":""}'),
('links', '{"random_url_length":7,"qr_code_reader_is_enabled":true,"domains_is_enabled":true,"additional_domains_is_enabled":true,"main_domain_is_enabled":true,"blacklisted_domains":"","blacklisted_keywords":"","google_safe_browsing_is_enabled":false,"google_safe_browsing_api_key":""}'),
('cookie_consent', '{"is_enabled":false,"logging_is_enabled":false,"necessary_is_enabled":true,"analytics_is_enabled":true,"targeting_is_enabled":true,"layout":"bar","position_y":"middle","position_x":"center"}'),
('license', '{\"license\":\"null\",\"type\":\"Extended License\"}'),
('product_info', '{\"version\":\"18.0.0\", \"code\":\"1800\"}');

-- SEPARATOR --

CREATE TABLE `projects` (
`project_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`name` varchar(128) DEFAULT NULL,
`color` varchar(16) DEFAULT '#000',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`project_id`),
UNIQUE KEY `project_id` (`project_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `domains` (
`domain_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`scheme` varchar(8) NOT NULL DEFAULT '',
`host` varchar(256) NOT NULL DEFAULT '',
`custom_index_url` varchar(256) DEFAULT NULL,
`custom_not_found_url` varchar(256) DEFAULT NULL,
`type` tinyint DEFAULT '1',
`is_enabled` tinyint DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`domain_id`),
KEY `user_id` (`user_id`),
KEY `domains_host_index` (`host`),
KEY `domains_type_index` (`type`),
CONSTRAINT `domains_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `links` (
`link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`domain_id` bigint unsigned DEFAULT NULL,
`project_id` bigint unsigned DEFAULT NULL,
`user_id` bigint unsigned DEFAULT NULL,
`pixels_ids` text COLLATE utf8mb4_unicode_ci,
`url` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`location_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`settings` text COLLATE utf8mb4_unicode_ci,
`pageviews` bigint unsigned DEFAULT '0',
`is_enabled` tinyint unsigned DEFAULT '1',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`link_id`),
UNIQUE KEY `link_id` (`link_id`) USING BTREE,
KEY `user_id` (`user_id`),
KEY `domain_id` (`domain_id`),
KEY `project_id` (`project_id`),
CONSTRAINT `links_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `links_ibfk_2` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`domain_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `links_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `statistics` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`link_id` bigint unsigned DEFAULT NULL,
`country_code` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`os_name` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`city_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`browser_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`referrer_host` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`referrer_path` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`device_type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`browser_language` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`utm_source` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`utm_medium` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`utm_campaign` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_unique` tinyint DEFAULT '0',
`datetime` datetime NOT NULL,
PRIMARY KEY (`id`),
KEY `datetime` (`datetime`),
KEY `link_id` (`link_id`) USING BTREE,
CONSTRAINT `statistics_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `qr_codes` (
`qr_code_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`link_id` bigint unsigned DEFAULT NULL,
`user_id` bigint unsigned NOT NULL,
`project_id` bigint unsigned DEFAULT NULL,
`name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
`type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`qr_code_logo` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`qr_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
`settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`embedded_data` text,
`datetime` datetime NOT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`qr_code_id`),
KEY `user_id` (`user_id`),
KEY `project_id` (`project_id`),
KEY `link_id` (`link_id`),
CONSTRAINT `qr_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `qr_codes_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `qr_codes_ibfk_3` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `pixels` (
`pixel_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned NOT NULL,
`type` varchar(64) NOT NULL,
`name` varchar(64) NOT NULL,
`pixel` varchar(64) NOT NULL,
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`pixel_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `pixels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- SEPARATOR --

CREATE TABLE IF NOT EXISTS `codes` (
  `code_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `discount` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `redeemed` int(11) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- SEPARATOR --

CREATE TABLE IF NOT EXISTS `redeemed_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- SEPARATOR --

CREATE TABLE IF NOT EXISTS `taxes` (
  `tax_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `value_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('inclusive','exclusive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_type` enum('personal','business','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countries` text COLLATE utf8mb4_unicode_ci,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- SEPARATOR --

CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_id` int(10) UNSIGNED DEFAULT NULL,
  `processor` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci,
  `billing` text COLLATE utf8mb4_unicode_ci,
  `business` text COLLATE utf8mb4_unicode_ci,
  `taxes_ids` text COLLATE utf8mb4_unicode_ci,
  `base_amount` float DEFAULT NULL,
  `total_amount` float DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` float DEFAULT NULL,
  `currency` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci