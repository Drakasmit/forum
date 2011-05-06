<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

Class PluginForum_ModuleForum_MapperForum extends Mapper {
	protected $oUserCurrent=null;

	public function SetUserCurrent($oUserCurrent)  {
		$this->oUserCurrent=$oUserCurrent;
	}

	public function AddForum(PluginForum_ModuleForum_EntityForum $oForum) {
		$sql = "INSERT INTO ".Config::Get('plugin.forum.table.forum_list')." 
			(forum_id,
			forum_parent_id,
			category_id,
			forum_title,
			forum_url,
			forum_moder,
			forum_sort		
			)
			VALUES(?, ?, ?, ?, ?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oForum->getId(),$oForum->getParentId(),$oForum->getCategoryId(),$oForum->getTitle(),$oForum->getUrl(),$oForum->getModer(),$oForum->getSort())) {
			return $iId;
		}		
		return false;
	}
	
	public function UpdateForum(PluginForum_ModuleForum_EntityForum $oForum) {
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_list')." 
			SET forum_parent_id = ? ,
			category_id = ? ,
			forum_title = ? ,
			forum_url = ? ,
			forum_moder = ? ,
			forum_sort = ?
			WHERE forum_id = ?d
		";			
		if ($this->oDb->query($sql,$oForum->getParentId(),$oForum->getCategoryId(),$oForum->getTitle(),$oForum->getUrl(),$oForum->getModer(),$oForum->getSort(),$oForum->getId())) 
		{
			return true;
		}		
		return false;
	}
	
	public function GetForumsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
				
		$sql = "SELECT 
					b.*							 
				FROM 
					".Config::Get('plugin.forum.table.forum_list')." as b					
				WHERE 
					b.forum_id IN(?a) 								
				ORDER BY FIELD(b.forum_id,?a) ";
		$aForums=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			foreach ($aRows as $aForum) {
				$aForums[]=Engine::GetEntity('PluginForum_Forum',$aForum);
			}
		}
		return $aForums;
	}	

	public function GetForums() {
		$sql = "SELECT
			b.forum_id
			FROM ".Config::Get('plugin.forum.table.forum_list')." as b
			";
		$aForums=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aForum) {
				$aForums[]=$aForum['forum_id'];
			}
		}
		return $aForums;
	}

	public function GetForumByUrl($sUrl) {		
		$sql = "SELECT 
				b.forum_id 
			FROM 
				".Config::Get('plugin.forum.table.forum_list')." as b
			WHERE 
				b.forum_url = ? 		
				";
		if ($aRow=$this->oDb->selectRow($sql,$sUrl)) {
			return $aRow['forum_id'];
		}
		return null;
	}
	
	public function GetForumsByCategoryId($Id) {		
		$sql = "SELECT 		
						forum_id										
					FROM 
						".Config::Get('plugin.forum.table.forum_list')."				
					WHERE 
						category_id = ?
					ORDER BY
						forum_sort DESC
						";
		
		$aForums=array();
		if ($aRows=$this->oDb->select($sql,$Id)) {
			foreach ($aRows as $aForum) {
				$aForums[]=$aForum['forum_id'];
			}
		}
		return $aForums;
	}
	
}
?>