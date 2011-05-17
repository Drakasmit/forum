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
			topic_position,
			topic_views,
			topic_count_posts,
			last_post_id
			)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus(),$oTopic->getPosition(),$oTopic->getCountViews(),$oTopic->getCountPosts(),$oTopic->getLastPostId())) {
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
			topic_position = ?,
			topic_views = ?,
			topic_count_posts = ?,
			last_post_id = ?
			WHERE topic_id = ?d
		";			
		if ($this->oDb->query($sql,$oTopic->getPostId(),$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus(),$oTopic->getPosition(),$oTopic->getCountViews(),$oTopic->getCountPosts(),$oTopic->getLastPostId(),$oTopic->getId())) 
		{
			return true;
		}		
		return false;
	}
	
	public function UpdateTopicRead(PluginForum_ModuleTopic_EntityTopicRead $oTopicRead) {		
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_read')." 
			SET 
				post_id_last = ?,
				date_read = ?
			WHERE
				topic_id = ?
				AND				
				user_id = ?
		";			
		return $this->oDb->query($sql,$oTopicRead->getPostIdLast(),$oTopicRead->getDateRead(),$oTopicRead->getTopicId(),$oTopicRead->getUserId());
	}	

	public function AddTopicRead(PluginForum_ModuleTopic_EntityTopicRead $oTopicRead) {		
		$sql = "INSERT INTO ".Config::Get('plugin.forum.table.forum_read')." 
			SET 
				post_id_last = ?,
				date_read = ?,
				topic_id = ?,							
				user_id = ? 
		";			
		return $this->oDb->query($sql,$oTopicRead->getPostIdLast(),$oTopicRead->getDateRead(),$oTopicRead->getTopicId(),$oTopicRead->getUserId());
	}
	/**
	 * Удаляет записи о чтении записей по списку идентификаторов
	 *
	 * @param  array $aTopicId
	 * @return bool
	 */				
	public function DeleteTopicReadByArrayId($aTopicId) {
		$sql = "
			DELETE FROM ".Config::Get('plugin.forum.table.forum_read')." 
			WHERE
				topic_id IN(?a)				
		";			
		if ($this->oDb->query($sql,$aTopicId)) {
			return true;
		}
		return false;
	}
			
	public function GetTopicsReadByArray($aArrayId,$sUserId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
				
		$sql = "SELECT 
					*							 
				FROM 
					".Config::Get('plugin.forum.table.forum_read')."
				WHERE 
					topic_id IN(?a)
					AND
					user_id = ?d 
				";
		$aReads=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$sUserId)) {
			foreach ($aRows as $aRow) {
				$aReads[]=Engine::GetEntity('PluginForum_Topic_TopicRead',$aRow);
			}
		}		
		return $aReads;
	}
	
	public function GetTopicsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
		$sql = "SELECT 
					*
				FROM 
					".Config::Get('plugin.forum.table.forum_topics')."
				WHERE
					topic_id IN (?a)";
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
					ORDER BY topic_position DESC, topic_date DESC
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
				topic_id
			FROM 
				".Config::Get('plugin.forum.table.forum_topics')."
			WHERE 
				topic_url = ?
				";
		if ($aRow=$this->oDb->selectRow($sql,$sUrl)) {
			return $aRow['topic_id'];
		}
		return null;
	}
	
	public function GetTopicsByForumsArray($aId) {	
		$sql = "SELECT 		
						topic_id
					FROM 
						".Config::Get('plugin.forum.table.forum_topics')."
					WHERE 
						forum_id IN (?a) 
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
	
	public function SetLastPostId($Id,$tId) {	
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET last_post_id = ?
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
	
	public function SetCountPosts($iCount,$Id) {	
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET topic_count_posts = ?
			WHERE topic_id = ?d
		";
		if ($aRows=$this->oDb->select($sql,$iCount,$Id)) {
			return true;
		}
		return false;
	}
	
	public function GetCountTopics() {
		$sql = "SELECT count(topic_id) as count FROM ".Config::Get('plugin.forum.table.forum_topics');			
		$result=$this->oDb->selectRow($sql);
		return $result['count'];
	}

}
?>