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
			topic_status
			)
			VALUES(?, ?, ?, ?, ?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus())) {
			$oTopic->setId($iId);
			return $iId;
		}		
		return false;
	}
	
	public function UpdateTopic(PluginForum_ModuleTopic_EntityTopic $oTopic) {
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_topics')." 
			SET forum_id = ?,
			user_id = ?,
			topic_title = ?,
			topic_url = ?,
			topic_date = ?,
			topic_status = ?
			WHERE topic_id = ?d
		";			
		if ($this->oDb->query($sql,$oTopic->getForumId(),$oTopic->getUserId(),$oTopic->getTitle(),$oTopic->getUrl(),$oTopic->getDate(),$oTopic->getStatus(),$oTopic->getId())) 
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
	
	public function GetTopicsByForumId($Id) {	
		$sql = "SELECT 		
						topic_id										
					FROM 
						".Config::Get('plugin.forum.table.forum_topics')."				
					WHERE 
						forum_id = ?
						";
		
		$aTopics=array();
		if ($aRows=$this->oDb->select($sql,$Id)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=$aTopic['topic_id'];
			}
		}
		return $aTopics;
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

	
}
?>