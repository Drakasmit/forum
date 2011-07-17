<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/
 
class PluginForum_ModuleTopic_EntityTopic extends Entity {

    public function getId() {
        return $this->_aData['topic_id'];
    }
    public function getPostId() {
        return $this->_aData['post_id'];
    }
    public function getForumId() {
        return $this->_aData['forum_id'];
    }
    public function getUserId() {
        return $this->_aData['user_id'];
    }
    public function getTitle() {
        return $this->_aData['topic_title'];
    }
    public function getUrl() {
        return $this->_aData['topic_url'];
    }
    public function getDate() {
        return $this->_aData['topic_date'];
    }
    public function getDateRead() {
        return $this->_aData['date_read'];
    }  
    public function getStatus() {
        return $this->_aData['topic_status'];
    }
    public function getPosition() {
        return $this->_aData['topic_position'];
    }
    public function getCountViews() {
        return $this->_aData['topic_views'];
    }

	public function setId($data) {
        $this->_aData['topic_id']=$data;
    }
	public function setPostId($data) {
        $this->_aData['post_id']=$data;
    }
	public function setForumId($data) {
        $this->_aData['forum_id']=$data;
    }
	public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
	public function setTitle($data) {
        $this->_aData['topic_title']=$data;
    }
	public function setUrl($data) {
        $this->_aData['topic_url']=$data;
    }
	public function setDate($data) {
        $this->_aData['topic_date']=$data;
    }
    public function setDateRead($data) {
        $this->_aData['date_read']=$data;
    }
	public function setStatus($data) {
        $this->_aData['topic_status']=$data;
    }
	public function setPosition($data) {
        $this->_aData['topic_position']=$data;
    }
	public function setCountViews($data) {
        $this->_aData['topic_views']=$data;
    }


}
?>