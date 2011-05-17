<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/
 
$config['topics']['per_page'] = 10; // Количество топиков на страницу
$config['posts']['per_page'] = 20; // Количество топиков на страницу
 
$config['table']['forum_list'] = '___db.table.prefix___forum_list';
$config['table']['forum_category'] = '___db.table.prefix___forum_category';
$config['table']['forum_topics'] = '___db.table.prefix___forum_topics';
$config['table']['forum_posts'] = '___db.table.prefix___forum_posts';
$config['table']['forum_read'] = '___db.table.prefix___forum_read';

Config::Set('router.page.forum', 'PluginForum_ActionForum');

return $config;
?>