<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/
 
$config=array();

/**
 * Количество топиков на страницу
 */
$config['topic_per_page']		= 20;
/**
 * Количество постов на страницу
 */
$config['post_per_page']		= 10;

/**
 * Максимальный размер поста в символах
 */
$config['post_max_length']		= 5000;

/**
 * Настройки роутера
 */
Config::Set('router.page.forum', 'PluginForum_ActionForum');

return $config;
?>