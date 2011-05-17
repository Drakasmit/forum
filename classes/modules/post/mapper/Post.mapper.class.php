<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

Class PluginForum_ModulePost_MapperPost extends Mapper {
	protected $oUserCurrent=null;

	public function SetUserCurrent($oUserCurrent)  {
		$this->oUserCurrent=$oUserCurrent;
	}

	public function AddPost(PluginForum_ModulePost_EntityPost $oPost) {
		$sql = "INSERT INTO ".Config::Get('plugin.forum.table.forum_posts')." 
			(forum_id,
			topic_id,
			user_id,
			post_date,
			post_text,
			post_text_source
			)
			VALUES(?, ?, ?, ?, ?, ?)";			
		if ($iId=$this->oDb->query($sql,$oPost->getForumId(),$oPost->getTopicId(),$oPost->getUserId(),$oPost->getDate(),$oPost->getText(),$oPost->getTextSource())) {
			$oPost->setId($iId);
			return $iId;
		}		
		return false;
	}
	
	public function UpdatePost(PluginForum_ModulePost_EntityPost $oPost) {
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_posts')." 
			SET forum_id = ?,
			topic_id = ?,
			user_id = ?,
			post_date = ?,
			post_text = ?,
			post_text_source = ?
			WHERE post_id = ?d";			
		if ($this->oDb->query($sql,$oPost->getForumId(),$oPost->getTopicId(),$oPost->getUserId(),$oPost->getDate(),$oPost->getText(),$oPost->getTextSource(),$oPost->getId())) 
		{
			return true;
		}		
		return false;
	}
	
	public function GetPostsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
		$sql = "SELECT 
					b.*
				FROM 
					".Config::Get('plugin.forum.table.forum_posts')." as b
				WHERE
					b.post_id IN (?a)";
		$aPosts=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId)) {
			foreach ($aRows as $aPost) {
				$aPosts[]=Engine::GetEntity('PluginForum_Post',$aPost);
			}
		}
		return $aPosts;
	}	
	
	public function GetPostsByTopicId($Id,&$iCount,$iPage,$iPerPage) {	
		$sql = "SELECT 		
						post_id										
					FROM 
						".Config::Get('plugin.forum.table.forum_posts')."
					WHERE 
						topic_id = ?
					LIMIT ?d, ?d";
		$aPosts=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql,$Id,($iPage-1)*$iPerPage, $iPerPage)) {
			foreach ($aRows as $aPost) {
				$aPosts[]=$aPost['post_id'];
			}
		}
		return $aPosts;
	}
	
	public function GetUserByPostId($Id) {	
		$sql = "SELECT 
						user_id
					FROM 
						".Config::Get('plugin.forum.table.forum_posts')."
					WHERE 
						post_id = ?";
		if ($aRow=$this->oDb->selectRow($sql,$Id)) {
			return $aRow['user_id'];
		}
		return null;
	}
	
	public function GetCountPosts() {
		$sql = "SELECT count(post_id) as count FROM ".Config::Get('plugin.forum.table.forum_posts');			
		$result=$this->oDb->selectRow($sql);
		return $result['count'];
	}	
	
	public function GetCountToDayPosts($sDate) {
		$sql = "SELECT count(post_id) as count FROM ".Config::Get('plugin.forum.table.forum_posts')." WHERE post_date >= ?";			
		$result=$this->oDb->selectRow($sql,$sDate);
		return $result['count'];
	}
	
}
?>