--
-- Структура таблицы `prefix_forum_category`
--

CREATE TABLE IF NOT EXISTS `prefix_forum_category` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `category_title` varchar(255) NOT NULL,
  PRIMARY KEY  (`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `prefix_forum_category`
--

INSERT INTO `prefix_forum_category` (`category_id`, `category_title`) VALUES
(1, 'Категория 1'),
(2, 'Категория 2');

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_forum_list`
--

CREATE TABLE IF NOT EXISTS `prefix_forum_list` (
  `forum_id` int(11) unsigned NOT NULL auto_increment,
  `forum_parent_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(11) unsigned NOT NULL,
  `forum_title` varchar(255) character set utf8 NOT NULL default '',
  `forum_url` varchar(255) character set utf8 NOT NULL default '',
  `forum_moder` varchar(250) character set utf8 NOT NULL,
  `forum_sort` int(11) NOT NULL,
  `post_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `forum_count_topics` int(11) NOT NULL,
  `forum_count_posts` int(11) NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `prefix_forum_list`
--

INSERT INTO `prefix_forum_list` (`forum_id`, `forum_parent_id`, `category_id`, `forum_title`, `forum_url`, `forum_moder`, `forum_sort`, `post_id`, `user_id`, `topic_id`, `forum_count_topics`, `forum_count_posts`) VALUES
(1, 0, 1, 'Тестовый форум', 'test', '1 2', 3, 87, 1, 97, 7, 1),
(3, 0, 1, 'Тестовый форум cat 1', 'testc1', '1 2', 1, 80, 1, 90, 0, 0),
(4, 0, 2, 'Тестовый форум cat 2', 'testc2', '1 2', 2, 81, 1, 91, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_forum_posts`
--

CREATE TABLE IF NOT EXISTS `prefix_forum_posts` (
  `post_id` int(11) unsigned NOT NULL auto_increment,
  `forum_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `post_date` datetime NOT NULL,
  `post_text` text NOT NULL,
  `post_text_source` text NOT NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88 ;

--
-- Дамп данных таблицы `prefix_forum_posts`
--

INSERT INTO `prefix_forum_posts` (`post_id`, `forum_id`, `topic_id`, `user_id`, `post_date`, `post_text`, `post_text_source`) VALUES
(74, 1, 84, 1, '2011-05-09 03:28:46', 'asdasdasdasdasdasd', 'asdasdasdasdasdasd'),
(75, 1, 85, 1, '2011-05-09 03:29:56', 'asdasdasdasd', 'asdasdasdasd'),
(76, 3, 86, 1, '2011-05-09 03:34:01', 'asdasdasdasdasdasdasd', 'asdasdasdasdasdasdasd'),
(77, 3, 87, 1, '2011-05-09 03:38:39', 'asdasdasdasdasdasd', 'asdasdasdasdasdasd'),
(78, 3, 88, 1, '2011-05-09 03:39:07', 'asdasdasdasdasdasd', 'asdasdasdasdasdasd'),
(79, 3, 89, 1, '2011-05-09 03:39:34', 'asdasdasdasdasd', 'asdasdasdasdasd'),
(80, 3, 90, 1, '2011-05-09 03:40:05', 'asdasdasdasdasd', 'asdasdasdasdasd'),
(81, 4, 91, 1, '2011-05-09 03:40:18', '123123123123123123', '123123123123123123'),
(82, 1, 92, 1, '2011-05-09 18:25:51', 'asdasdasdasd', 'asdasdasdasd'),
(83, 1, 93, 1, '2011-05-09 18:25:59', '123123123', '123123123'),
(85, 1, 95, 1, '2011-05-09 19:34:02', 'фывфывфывфывфывфывфывфывфывфывф', 'фывфывфывфывфывфывфывфывфывфывф'),
(86, 1, 96, 1, '2011-05-09 19:37:33', 'Дадада', 'Дадада'),
(87, 1, 97, 1, '2011-05-09 20:41:45', '<b>asdasdasdasd</b><br/>\r\n<blockquote>asdasdasd</blockquote><object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/vR693MTwzq8&hl=en"></param><param name="wmode" value="opaque"></param><embed src="http://www.youtube.com/v/vR693MTwzq8&hl=en" type="application/x-shockwave-flash" wmode="opaque" width="425" height="344"></embed></object>', '<b>asdasdasdasd</b>\r\n<blockquote>asdasdasd</blockquote>\r\n<video>http://www.youtube.com/watch?v=vR693MTwzq8</video>');

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_forum_topics`
--

CREATE TABLE IF NOT EXISTS `prefix_forum_topics` (
  `topic_id` int(11) unsigned NOT NULL auto_increment,
  `post_id` int(11) unsigned default NULL,
  `forum_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `topic_title` varchar(255) NOT NULL,
  `topic_url` varchar(255) NOT NULL,
  `topic_date` datetime NOT NULL,
  `topic_status` int(11) unsigned NOT NULL,
  `topic_views` int(11) NOT NULL,
  `topic_count_posts` int(11) NOT NULL,
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;

--
-- Дамп данных таблицы `prefix_forum_topics`
--

INSERT INTO `prefix_forum_topics` (`topic_id`, `post_id`, `forum_id`, `user_id`, `topic_title`, `topic_url`, `topic_date`, `topic_status`, `topic_views`, `topic_count_posts`) VALUES
(84, 74, 1, 1, '1111111111111', 'test', '2011-05-09 03:28:46', 1, 0, 0),
(85, 75, 1, 1, 'asdasdasd', 'test', '2011-05-09 03:29:56', 1, 2, 0),
(86, 76, 3, 1, 'asdasdasdasdasdasdas', 'test', '2011-05-09 03:34:01', 1, 0, 0),
(87, 77, 3, 1, 'asdasdasdasd', 'test', '2011-05-09 03:38:39', 1, 0, 0),
(88, 78, 3, 1, 'asdasdasdasd', 'test', '2011-05-09 03:39:07', 1, 0, 0),
(89, 79, 3, 1, 'asdasdasd', 'test', '2011-05-09 03:39:34', 1, 0, 0),
(90, 80, 3, 1, 'asdasdasd', 'test', '2011-05-09 03:40:05', 1, 0, 0),
(91, 81, 4, 1, '123123123', 'test', '2011-05-09 03:40:18', 1, 0, 0),
(92, 82, 1, 1, 'asdasdasdasdasdasdasdasdasdasd', 'test', '2011-05-09 18:25:51', 1, 2, 0),
(93, 83, 1, 1, '123123', 'test', '2011-05-09 18:25:59', 1, 2, 0),
(95, 85, 1, 1, 'фывфывфывфывфывфыв', 'test', '2011-05-09 19:34:02', 1, 8, 0),
(96, 86, 1, 1, 'Теперь работает счетчик количества просмотров', 'test', '2011-05-09 19:37:33', 1, 9, 0),
(97, 87, 1, 1, 'asdasdasdasdasdasd', 'test', '2011-05-09 20:41:45', 1, 3, 0);