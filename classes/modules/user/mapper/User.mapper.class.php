<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

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