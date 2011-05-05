<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleCategory_EntityCategory extends Entity {
    public function getId() {
        return $this->_aData['category_id'];
    }
    public function getTitle() {
        return $this->_aData['category_title'];
    }



	public function setId($data) {
        $this->_aData['category_id']=$data;
    }
	public function setTitle($data) {
        $this->_aData['category_title']=$data;
    }

}
?>