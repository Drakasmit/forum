CREATE TABLE IF NOT EXISTS `prefix_forum` (
  `forum_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `forum_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL,
  `forum_title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `forum_description` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `forum_url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `forum_moder` varchar(250) CHARACTER SET utf8 NOT NULL,
  `forum_sort` int(11) NOT NULL,
  `post_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`forum_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_forum_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_title` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_forum_post` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `post_date` datetime NOT NULL,
  `post_text` text NOT NULL,
  `post_text_source` text NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_forum_topic` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned DEFAULT NULL,
  `forum_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `topic_title` varchar(255) NOT NULL,
  `topic_url` varchar(255) NOT NULL,
  `topic_date` datetime NOT NULL,
  `topic_status` int(11) NOT NULL DEFAULT '0',
  `topic_position` int(11) NOT NULL DEFAULT '0',
  `topic_views` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_forum_read` (
  `topic_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `read_date` datetime NOT NULL,
  `post_id_last` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;