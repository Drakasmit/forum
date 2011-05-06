CREATE TABLE IF NOT EXISTS `prefix_forum_category` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `category_title` varchar(255) NOT NULL,
  PRIMARY KEY  (`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


INSERT INTO `prefix_forum_category` (`category_id`, `category_title`) VALUES
(1, 'Категория 1'),
(2, 'Категория 2');


CREATE TABLE IF NOT EXISTS `prefix_forum_list` (
  `forum_id` int(11) unsigned NOT NULL auto_increment,
  `forum_parent_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(11) unsigned NOT NULL,
  `forum_title` varchar(255) character set utf8 NOT NULL default '',
  `forum_url` varchar(255) character set utf8 NOT NULL default '',
  `forum_moder` varchar(250) character set utf8 NOT NULL,
  `forum_sort` int(11) NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;


INSERT INTO `prefix_forum_list` (`forum_id`, `forum_parent_id`, `category_id`, `forum_title`, `forum_url`, `forum_moder`, `forum_sort`) VALUES
(1, 0, 1, 'Тестовый форум', 'test', '1 2', 3),
(2, 0, 2, 'Тестовый форум 2', 'test2', '1 2', 4),
(3, 0, 1, 'Тестовый форум cat 1', 'testc1', '1 2', 1),
(4, 0, 2, 'Тестовый форум cat 2', 'testc2', '1 2', 2);

CREATE TABLE IF NOT EXISTS `prefix_forum_posts` (
  `post_id` int(11) unsigned NOT NULL auto_increment,
  `topic_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `post_date` datetime NOT NULL,
  `post_text` text NOT NULL,
  `post_text_source` text NOT NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

INSERT INTO `prefix_forum_posts` (`post_id`, `topic_id`, `user_id`, `post_date`, `post_text`, `post_text_source`) VALUES
(40, 48, 1, '2011-05-05 09:29:57', 'Сейчас он должен добавиться так, как надо, иначе я застрелюсь.', 'Сейчас он должен добавиться так, как надо, иначе я застрелюсь.'),
(41, 49, 1, '2011-05-05 12:30:11', 'фывфывфывфывфывфывфывфыв', 'фывфывфывфывфывфывфывфыв'),
(42, 50, 1, '2011-05-05 14:38:39', 'asdasdasdasdasdasdasdasdasdasd', 'asdasdasdasdasdasdasdasdasdasd'),
(43, 51, 1, '2011-05-05 14:42:21', 'dasdasdasdasdasd', 'dasdasdasdasdasd'),
(44, 52, 1, '2011-05-05 14:44:03', 'sdasdasdasdasd', 'sdasdasdasdasd'),
(45, 53, 1, '2011-05-05 14:44:14', 'ddddddddddd', 'ddddddddddd'),
(46, 54, 1, '2011-05-05 14:51:35', '312', '312'),
(47, 55, 1, '2011-05-05 14:51:42', '3123123123', '3123123123'),
(48, 56, 1, '2011-05-05 14:51:49', '523453453453453454', '523453453453453454'),
(49, 57, 1, '2011-05-05 14:51:56', '35sdfsdfasdfasdf', '35sdfsdfasdfasdf'),
(50, 58, 1, '2011-05-05 15:20:28', '4565768567546', '4565768567546'),
(51, 58, 2, '2011-05-05 15:21:09', '12312312312312312312', '12312312312312312312');

CREATE TABLE IF NOT EXISTS `prefix_forum_topics` (
  `topic_id` int(11) unsigned NOT NULL auto_increment,
  `forum_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `topic_title` varchar(255) NOT NULL,
  `topic_url` varchar(255) NOT NULL,
  `topic_date` datetime NOT NULL,
  `topic_status` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

INSERT INTO `prefix_forum_topics` (`topic_id`, `forum_id`, `user_id`, `topic_title`, `topic_url`, `topic_date`, `topic_status`) VALUES
(49, 2, 1, 'Для теста вывода последних тем', 'test', '2011-05-05 00:00:00', 1),
(50, 3, 1, 'asdasdasdasdasd', 'test', '2011-05-05 00:00:00', 1),
(51, 3, 1, 'asdasdasdas', 'test', '2011-05-05 00:00:00', 1),
(52, 3, 1, 'asdasdasdasda', 'test', '2011-05-05 00:00:00', 1),
(53, 2, 1, 'aaaaaaaaa', 'test', '2011-05-05 00:00:00', 1),
(55, 3, 1, '1231231', 'test', '2011-05-05 00:00:00', 1),
(56, 2, 1, '12341234235', 'test', '2011-05-05 00:00:00', 1),
(58, 1, 1, '123123123', 'test', '2011-05-05 15:20:28', 1);
