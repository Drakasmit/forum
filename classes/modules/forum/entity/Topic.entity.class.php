<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleForum_EntityTopic extends EntityORM {
	protected $aRelations = array(
		'forum'=>array('belongs_to','PluginForum_ModuleForum_EntityForum','forum_id'),
		'user'=>array('belongs_to','ModuleUser_EntityUser','user_id'),
		'post'=>array('belongs_to','PluginForum_ModuleForum_EntityPost','last_post_id')
	);
	
	public function getCountPosts() {
		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByTopicId($this->getId(), array('#page'=>array(1,1)));
		return $aResult['count'];
	}
}
?>