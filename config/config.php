<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/
 
$config=array();
 
$config['topics']['per_page']=10; // Количество топиков на страницу
$config['posts']['per_page']=20; // Количество топиков на страницу

Config::Set('router.page.forum', 'PluginForum_ActionForum');

$config['table']['forum_list'] = '___db.table.prefix___forum';
$config['table']['forum_category'] = '___db.table.prefix___forum_category';
$config['table']['forum_topics'] = '___db.table.prefix___forum_topic';
$config['table']['forum_posts'] = '___db.table.prefix___forum_post';
$config['table']['forum_read'] = '___db.table.prefix___forum_read';

return $config;
?>