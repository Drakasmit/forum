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
		'topic'=>array('belongs_to','PluginForum_ModuleForum_EntityTopic','topic_id'),
		'forum'=>array('belongs_to','PluginForum_ModuleForum_EntityForum','forum_id'),
	);

	public function getUrlFull() {
		$oTopic=$this->getTopic();
		/**
		 * ќпредел€ем на какой странице находитс€ пост
		 */
		$sPage='';
		if ($oTopic->getCountPost() > Config::Get('plugin.forum.post_per_page')) {
			$iPage=1;
			if ($iCountPage=ceil($oTopic->getCountPost()/Config::Get('plugin.forum.post_per_page'))) {
				$iPage=$iCountPage-$iPage+1;
			}
			$iPage=$iPage ? $iPage : 1;
			if ($iPage > 1) {
				$sPage="page{$iPage}";
			}
		}
		/**
		 * якорь
		 */
		$sAnchor="#post{$this->getId()}";

		return Router::GetPath('forum')."topic/{$oTopic->getId()}/{$sPage}{$sAnchor}";
	}
}
?>