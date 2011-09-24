<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleForum_EntityPost extends EntityORM {
	protected $aRelations = array(
		'user'=>array('belongs_to','ModuleUser_EntityUser','user_id'),
		'topic'=>array('belongs_to','PluginForum_ModuleForum_EntityPost','topic_id'),
		'forum'=>array('belongs_to','PluginForum_ModuleForum_EntityForum','forum_id'),
	);
}
?>