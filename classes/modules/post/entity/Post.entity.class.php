<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModulePost_EntityPost extends Entity {
    public function getId() {
        return $this->_aData['post_id'];
    }
    public function getForumId() {
        return $this->_aData['forum_id'];
    }
    public function getTopicId() {
        return $this->_aData['topic_id'];
    }
    public function getUserId() {
        return $this->_aData['user_id'];
    }
    public function getDate() {
        return $this->_aData['post_date'];
    }
    public function getText() {
        return $this->_aData['post_text'];
    }
    public function getTextSource() {
        return $this->_aData['post_text_source'];
    }
    public function getUser() {
        return $this->_aData['user'];
    }



	public function setId($data) {
        $this->_aData['post_id']=$data;
    }
	public function setForumId($data) {
        $this->_aData['forum_id']=$data;
    }
	public function setTopicId($data) {
        $this->_aData['topic_id']=$data;
    }
	public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
	public function setDate($data) {
        $this->_aData['post_date']=$data;
    }
	public function setText($data) {
        $this->_aData['post_text']=$data;
    }
	public function setTextSource($data) {
        $this->_aData['post_text_source']=$data;
    }


}
?>