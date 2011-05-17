<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleTopic_EntityTopicRead extends Entity {

    public function getTopicId() {
        return $this->_aData['topic_id'];
    } 
    public function getPostIdLast() {
        return $this->_aData['post_id_last'];
    }  
    public function getUserId() {
        return $this->_aData['user_id'];
    }
    public function getDateRead() {
        return $this->_aData['date_read'];
    }
    
    
	public function setTopicId($data) {
        $this->_aData['topic_id']=$data;
    }
	public function setPostIdLast($data) {
        $this->_aData['post_id_last']=$data;
    }
    public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
    public function setDateRead($data) {
        $this->_aData['date_read']=$data;
    }

}
?>