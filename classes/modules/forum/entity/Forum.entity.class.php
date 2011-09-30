<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleForum_EntityForum extends EntityORM {
	protected $aRelations = array(
		'tree',
		'user'=>array('belongs_to','ModuleUser_EntityUser','last_user_id'),
		'topic'=>array('belongs_to','PluginForum_ModuleForum_EntityTopic','last_topic_id'),
		'post'=>array('belongs_to','PluginForum_ModuleForum_EntityPost','last_post_id'),
	);
	
	public function getCountPosts() {
		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByForumId($this->getId(), array('#page'=>array(1,1)));
		return $aResult['count'];
	}
	
	public function getCountTopics() {
		$aResult=$this->PluginForum_ModuleForum_GetTopicItemsByForumId($this->getId(), array('#page'=>array(1,1)));
		return $aResult['count'];
	}
	
}
?>