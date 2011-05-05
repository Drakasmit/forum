<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleForum_EntityForum extends Entity {
    public function getId() {
        return $this->_aData['forum_id'];
    }
    public function getParentId() {
        return $this->_aData['forum_parent_id'];
    }
    public function getCategoryId() {
        return $this->_aData['category_id'];
    }
    public function getTitle() {
        return $this->_aData['forum_title'];
    }
    public function getUrl() {
        return $this->_aData['forum_url'];
    }
    public function getModer() {
        return $this->_aData['forum_moder'];
    }
    public function getSort() {
        return $this->_aData['forum_sort'];
    }
    public function getTopic() {
        return $this->_aData['topic'];
    }



	public function setId($data) {
        $this->_aData['forum_id']=$data;
    }
	public function setParentId($data) {
        $this->_aData['forum_parent_id']=$data;
    }
	public function setCategoryId($data) {
        $this->_aData['category_id']=$data;
    }
	public function setTitle($data) {
        $this->_aData['forum_title']=$data;
    }
	public function setUrl($data) {
        $this->_aData['forum_url']=$data;
    }
	public function setModer($data) {
        $this->_aData['forum_moder']=$data;
    }
	public function setSort($data) {
        $this->_aData['forum_sort']=$data;
    }
	public function setTopic($data) {
        $this->_aData['topic']=$data;
    }

}
?>