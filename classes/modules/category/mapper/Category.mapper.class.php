<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

Class PluginForum_ModuleCategory_MapperCategory extends Mapper {

	
	public function AddCategory(PluginForum_ModuleCategory_EntityCategory $oCategory) {
		$sql = "INSERT INTO ".Config::Get('plugin.forum.table.forum_category')." 
			(
			category_title	
			)
			VALUES(?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oCategory->getTitle())) {
			return $iId;
		}		
		return false;
	}
	
	public function UpdateCategory(PluginForum_ModuleCategory_EntityCategory $oCategory) {
		$sql = "UPDATE ".Config::Get('plugin.forum.table.forum_category')." 
			SET category_title = ?
			WHERE category_id = ?d
		";			
		if ($this->oDb->query($sql,$oCategory->getTitle(),$oCategory->getId())) 
		{
			return true;
		}		
		return false;
	}
	
	
	public function GetCategories() {
		$sql = "SELECT 
			b.category_id			 
			FROM ".Config::Get('plugin.forum.table.forum_category')." as b
			";	
		$aCategories=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aCategory) {
				$aCategories[]=$aCategory['category_id'];
			}
		}
		return $aCategories;
	}
	
	public function GetCategoriesByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
				
		$sql = "SELECT 
					b.*							 
				FROM 
					".Config::Get('plugin.forum.table.forum_category')." as b					
				WHERE 
					b.category_id IN(?a) 								
				ORDER BY FIELD(b.category_id,?a) ";
		$aCategories=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			foreach ($aRows as $aCategory) {
				$aCategories[]=Engine::GetEntity('PluginForum_Category',$aCategory);
			}
		}
		return $aCategories;
	}
	
	public function GetCategoryById($Id) {
		$sql = "SELECT 
				b.*
			FROM 
				".Config::Get('plugin.forum.table.forum_category')." as b
			WHERE 
				b.category_id = ? 		
				";
		if ($aRow=$this->oDb->selectRow($sql,$Id)) {
			return $aRow['category_id'];
		}
		return null;
	}
	
	public function DeleteCategory($Id) {
		$sql = "DELETE FROM ".Config::Get('plugin.forum.table.forum_category')." WHERE category_id = ?";
		return $aRow=$this->oDb->selectRow($sql,$Id);
	}

}
?>