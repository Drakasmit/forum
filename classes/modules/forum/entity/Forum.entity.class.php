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
    public function getDescription() {
        return $this->_aData['forum_description'];
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
    public function getPostId() {
        return $this->_aData['post_id'];
    }
    public function getUserId() {
        return $this->_aData['user_id'];
    }
    public function getTopicId() {
        return $this->_aData['topic_id'];
    }
    public function getCountTopics() {
        return $this->_aData['forum_count_topics'];
    }
    public function getCountPosts() {
        return $this->_aData['forum_count_posts'];
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
	public function setDescription($data) {
        $this->_aData['forum_description']=$data;
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
	public function setPostId($data) {
        $this->_aData['post_id']=$data;
    }
	public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
	public function setTopicId($data) {
        $this->_aData['topic_id']=$data;
    }
	public function setCountTopics($data) {
        $this->_aData['forum_count_topics']=$data;
    }
	public function setCountPosts($data) {
        $this->_aData['forum_count_posts']=$data;
    }
	public function setTopic($data) {
        $this->_aData['topic']=$data;
    }

}
?>