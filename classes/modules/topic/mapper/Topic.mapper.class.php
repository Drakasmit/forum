<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

Class PluginForum_ModuleTopic_MapperTopic extends Mapper {
	protected $oUserCurrent=null;

	public function SetUserCurrent($oUserCurrent)  {
		$this->oUserCurrent=$oUserCurrent;
	}

	public function AddTopic(PluginForum_ModuleTopic_EntityTopic $oTopic) {
		$sql = "INSERT INTO ".Config::Get('plugin.forum.table.forum_topics')." 
			(forum_id,
			user_id,
			topic_title,
			topic_url,
			topic_date,
			topic_status,
			topic_views,
			topic_count_posts
			)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus(),$oTopic->getCountViews(),$oTopic->getCountPosts())) {
			$oTopic->setId($iId);
			return $iId;
		}		
		return false;
	}
	
	public function UpdateTopic(PluginForum_ModuleTopic_EntityTopic $oTopic) {
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET post_id = ?,
			forum_id = ?,
			user_id = ?,
			topic_title = ?,
			topic_url = ?,
			topic_date = ?,
			topic_status = ?,
			topic_views = ?,
			topic_count_posts = ?
			WHERE topic_id = ?d
		";			
		if ($this->oDb->query($sql,$oTopic->getPostId(),$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus(),$oTopic->getCountViews(),$oTopic->getCountPosts(),$oTopic->getId())) 
		{
			return true;
		}		
		return false;
	}
	
	public function GetTopicsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
		$sql = "SELECT 
					b.*
				FROM 
					".Config::Get('plugin.forum.table.forum_topics')." as b
				WHERE
					b.topic_id IN (?a)";
		$aTopics=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=Engine::GetEntity('PluginForum_Topic',$aTopic);
			}
		}
		return $aTopics;
	}	
	
	public function GetTopicsByForumId($Id,&$iCount,$iPage,$iPerPage) {	
		$sql = "SELECT 		
						topic_id										
					FROM 
						".Config::Get('plugin.forum.table.forum_topics')."				
					WHERE 
						forum_id = ?
					ORDER BY topic_date DESC
					LIMIT ?d, ?d";
		
		$aTopics=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql,$Id,($iPage-1)*$iPerPage, $iPerPage)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=$aTopic['topic_id'];
			}
		}
		return $aTopics;
	}
	
	public function GetTopicByForumId($Id) {	
		$sql = "SELECT 		
						topic_id										
					FROM 
						".Config::Get('plugin.forum.table.forum_topics')."				
					WHERE 
						forum_id = ?
					ORDER BY topic_date DESC
						";
		
		if ($aRow=$this->oDb->selectRow($sql,$Id)) {
			return $aRow['topic_id'];
		}
		return null;
	}
	
	public function GetTopicByUrl($sUrl) {	
		$sql = "SELECT 
				b.topic_id
			FROM 
				".Config::Get('plugin.forum.table.forum_topics')." as b
			WHERE 
				b.topic_url = ?
				";
		if ($aRow=$this->oDb->selectRow($sql,$sUrl)) {
			return $aRow['topic_id'];
		}
		return null;
	}
	
	public function GetTopicsByForumsArray($aId) {	
		$sql = "SELECT 		
						b.topic_id
					FROM 
						".Config::Get('plugin.forum.table.forum_topics')." as b
					WHERE 
						b.forum_id IN (?a) 
						";
		$aTopics=array();
		if ($aRows=$this->oDb->select($sql,$aId)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=$aTopic['topic_id'];
			}
		}
		return $aTopics;
	}
	
	public function SetPostId($Id,$tId) {	
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET post_id = ?
			WHERE topic_id = ?d
		";
		if ($aRows=$this->oDb->select($sql,$Id,$tId)) {
			return true;
		}
		return false;
	}

	public function SetCountViews($iCount,$Id) {	
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET topic_views = ?
			WHERE topic_id = ?d
		";
		if ($aRows=$this->oDb->select($sql,$iCount,$Id)) {
			return true;
		}
		return false;
	}

}
?>