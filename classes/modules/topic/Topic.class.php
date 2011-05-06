<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/
 
class PluginForum_ModuleTopic extends Module {
	/**
	 * @var Mapper
	 */
	protected $oMapperTopic;	
	protected $oUserCurrent=null;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapperTopic=Engine::GetMapper(__CLASS__);
		$this->oMapperTopic->SetUserCurrent($this->User_GetUserCurrent());
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}
	
	/**
	 * Добавляет топик
	 *
	 * @param ModuleTopic_EntityTopic $oTopic
	 * @return unknown
	 */
	public function AddTopic(PluginForum_ModuleTopic_EntityTopic $oTopic) {
		if ($sId=$this->oMapperTopic->AddTopic($oTopic)) {
			$oTopic->setId($sId);
			//чистим зависимые кеши
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('forum_topic_new',"forum_topic_update_user_{$oTopic->getUserId()}","forum_topic_new_forum_{$oTopic->getForumId()}"));						
			return $oTopic;
		}
		return false;
	}
	
	public function GetTopicsByArrayId($aTopicId) {
		if (!$aTopicId) {
			return array();
		}
		if (!is_array($aTopicId)) {
			$aTopicId=array($aTopicId);
		}
		$aTopicId=array_unique($aTopicId);
		$aTopics=array();
		$aTopicIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aTopicId,'forum_topic_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {			
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {	
					if ($data[$sKey]) {
						$aTopics[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aTopicIdNotNeedQuery[]=$sValue;
					}
				} 
			}
		}
		/**
		 * Смотрим каких блогов не было в кеше и делаем запрос в БД
		 */		
		$aBTopicIdNeedQuery=array_diff($aTopicId,array_keys($aTopics));		
		$aBTopicIdNeedQuery=array_diff($aBTopicIdNeedQuery,$aTopicIdNotNeedQuery);		
		$aTopicIdNeedStore=$aBTopicIdNeedQuery;
		if ($data = $this->oMapperTopic->GetTopicsByArrayId($aBTopicIdNeedQuery)) {
			foreach ($data as $oTopic) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aTopics[$oTopic->getId()]=$oTopic;
				$this->Cache_Set($oTopic, "forum_topic_{$oTopic->getId()}", array(), 60*60*24*4);
				$aTopicIdNeedStore=array_diff($aTopicIdNeedStore,array($oTopic->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aTopicIdNeedStore as $sId) {
			$this->Cache_Set(null, "forum_topic_{$sId}", array(), 60*60*24*4);
		}		
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aTopics=func_array_sort_by_keys($aTopics,$aTopicId);
		return $aTopics;		
	}
	
	public function GetTopicsAdditionalData($aTopicId,$aAllowData=array('user'=>array(),'forum'=>array())) {
		func_array_simpleflip($aAllowData);
		if (!is_array($aTopicId)) {
			$aTopicId=array($aTopicId);
		}
		/**
		 * Получаем "голые" топики
		 */
		$aTopics=$this->GetTopicsByArrayId($aTopicId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();
		$aForumId=array();
		$aTopicIdQuestion=array();		
		foreach ($aTopics as $oTopic) {
			if (isset($aAllowData['user'])) {
				$aUserId[]=$oTopic->getUserId();
			}
			if (isset($aAllowData['forum'])) {
				$aForumId[]=$oTopic->getForumId();
			}
		}
		/**
		 * Получаем дополнительные данные
		 */
		if (isset($aAllowData['user'])) {
			$aUsers=$this->User_GetUsersAdditionalData($aUserId);
		}
		if (isset($aAllowData['forum']) ) {
			$aForums=$this->PluginForum_ModuleForum_GetForumsAdditionalData($aForumId);
		}
		/**
		 * Добавляем данные к результату - списку топиков
		 */
		foreach ($aTopics as $oTopic) {
			if (isset($aUsers[$oTopic->getUserId()])) {
				$oTopic->setUser($aUsers[$oTopic->getUserId()]);
			} else {
				$oTopic->setUser(null); // или $oTopic->setUser(new ModuleUser_EntityUser());
			}
			if (isset($aForums[$oTopic->getForumId()])) {
				$oTopic->setForum($aForums[$oTopic->getForumId()]);
			} else {
				$oTopic->setForum(null); // или $oTopic->setBlog(new ModuleBlog_EntityBlog());
			}					
		}
		return $aTopics;
	}
	
	public function GetTopicById($sTopicId) {
		$aTopics=$this->GetTopicsByArrayId($sTopicId);
		if (isset($aTopics[$sTopicId])) {
			return $aTopics[$sTopicId];
		}
		return null;
	}
	
	public function GetTopicsByForumId($Id) {
		if (false === ($data = $this->Cache_Get("topic_{$Id}"))) {			
			$data = array('collection'=>$this->oMapperTopic->GetTopicsByForumId($Id));
			$this->Cache_Set($data, "topic_{$Id}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetTopicsAdditionalData($data['collection']);
		return $data;
	}
	
	public function GetTopicByUrl($sUrl) {
		if (false === ($id = $this->Cache_Get("topic_url_{$sUrl}"))) {
			if ($id = $this->oMapperTopic->GetTopicByUrl($sUrl)) {
				$this->Cache_Set($id, "topic_url_{$sUrl}", array("topic_update_{$id}"), 60*60*24*2);
			} else {
				$this->Cache_Set(null, "topic_url_{$sUrl}", array('topic_update_','topic_new'), 60*60);
			}
		}
		return $this->GetTopicById($id);
	}
	
	public function GetTopicsByForumsArray($aId=array()) {
		if (false === ($data = $this->Cache_Get("topic_{$aId}"))) {			
			$data = array('collection'=>$this->oMapperTopic->GetTopicsByForumsArray($aId));
			$this->Cache_Set($data, "topic_{$aId}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetTopicsByArrayId($data['collection']);
		return $data;
	}

}
?>