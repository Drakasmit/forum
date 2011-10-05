<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleForum_EntityTopic extends EntityORM {
	protected $aRelations = array(
		'user'=>array('belongs_to','ModuleUser_EntityUser','user_id'),
		'forum'=>array('belongs_to','PluginForum_ModuleForum_EntityForum','forum_id'),
		'post'=>array('belongs_to','PluginForum_ModuleForum_EntityPost','last_post_id')
	);

	public function getPaging() {
		$oEngine=Engine::getInstance();
		$oForum=$this->getForum();
		$aPaging=$oEngine->Viewer_MakePaging(
			$this->getCountPost(),
			1,Config::Get('plugin.forum.post_per_page'),4,
			Router::GetPath('forum')."topic/{$this->getId()}"
		);
		return $aPaging;
	}

	public function getUrlFull() {
		return Router::GetPath('forum').'topic/'.$this->getId().'/';
	}
}
?>