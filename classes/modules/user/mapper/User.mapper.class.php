<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleUser_MapperUser extends Mapper {

	public function GetCountPosts($Id) {
		$sql = "SELECT count(post_id) as count FROM ".Config::Get('plugin.forum.table.forum_posts')."  WHERE user_id = ?";			
		$result=$this->oDb->selectRow($sql,$Id);
		return $result['count'];
	}

	public function GetCountTopics($Id) {
		$sql = "SELECT count(topic_id) as count FROM ".Config::Get('plugin.forum.table.forum_topics')." WHERE user_id= ?";			
		$result=$this->oDb->selectRow($sql,$Id);
		return $result['count'];
	}

	public function GetCountArticles($Id) {
		$sql = "SELECT count(topic_id) as count FROM ".Config::Get('db.table.topic')." WHERE user_id= ?";			
		$result=$this->oDb->selectRow($sql,$Id);
		return $result['count'];
	}

	public function GetCountComments($Id) {
		$sql = "SELECT count(comment_id) as count FROM ".Config::Get('db.table.comment')." WHERE user_id= ?";			
		$result=$this->oDb->selectRow($sql,$Id);
		return $result['count'];
	}

}
?>