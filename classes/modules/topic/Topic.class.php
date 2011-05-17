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
	
	public function GenerateUrl($sText) {
			$aConverter=array(  
				'а' => 'a',   'б' => 'b',   'в' => 'v',  
				'г' => 'g',   'д' => 'd',   'е' => 'e',  
				'ё' => 'e',   'ж' => 'zh',  'з' => 'z',  
				'и' => 'i',   'й' => 'y',   'к' => 'k',  
				'л' => 'l',   'м' => 'm',   'н' => 'n',  
				'о' => 'o',   'п' => 'p',   'р' => 'r',  
				'с' => 's',   'т' => 't',   'у' => 'u',  
				'ф' => 'f',   'х' => 'h',   'ц' => 'c',  
				'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  
				'ь' => "'",  'ы' => 'y',   'ъ' => "'",  
				'э' => 'e',   'ю' => 'yu',  'я' => 'ya',  
		  
				'А' => 'a',   'Б' => 'b',   'В' => 'v',  
				'Г' => 'g',   'Д' => 'd',   'Е' => 'e',  
				'Ё' => 'e',   'Ж' => 'zh',  'З' => 'z',  
				'И' => 'i',   'Й' => 'y',   'К' => 'k',  
				'Л' => 'l',   'М' => 'm',   'Н' => 'n',  
				'О' => 'o',   'П' => 'p',   'Р' => 'r',  
				'С' => 's',   'Т' => 't',   'У' => 'u',  
				'Ф' => 'f',   'Х' => 'h',   'Ц' => 'c',  
				'Ч' => 'ch',  'Ш' => 'sh',  'Щ' => 'sch',  
				'Ь' => "'",  'Ы' => 'y',   'Ъ' => "'",  
				'Э' => 'e',   'Ю' => 'yu',  'Я' => 'ya', 
				
				" "=> "-", "."=> "", "/"=> "-" 
			);  
			$sRes=strtr($sText,$aConverter);
			if ($sResIconv=@iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $sRes)) {
				$sRes=$sResIconv;
			}
			if (preg_match('/[^A-Za-z0-9_\-]/', $sRes)) {    	
				$sRes = preg_replace('/[^A-Za-z0-9_\-]/', '', $sRes);
				$sRes = preg_replace('/\-+/', '-', $sRes);
			}
			return $sRes;
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
		 * Временный минихак для добавления даты прочтения топика
		 */
		if ($this->oUserCurrent) {
			$aTopicsRead=$this->GetTopicsReadByArray($aTopicId,$this->oUserCurrent->getId());
		}
		foreach ($aTopics as $oTopic) {
			if (isset($aTopicsRead[$oTopic->getId()]))	{		
				$oTopic->setDateRead($aTopicsRead[$oTopic->getId()]->getDateRead());
			} else {
				$oTopic->setDateRead(date("Y-m-d H:i:s"));
			}	
		}
		/***** Кончилсо минихак *****/
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
		$aTopicsRead=array();
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
		if ($this->oUserCurrent) {
			$aTopicsRead=$this->GetTopicsReadByArray($aTopicId,$this->oUserCurrent->getId());
		}
		/**
		 * Добавляем данные к результату - списку топиков
		 */
		foreach ($aTopics as $oTopic) {
			$oTopic->setUser($aUsers[$oTopic->getUserId()]);
			$oTopic->setForum($aForums[$oTopic->getForumId()]);
			$oTopic->setPost($this->PluginForum_ModulePost_GetPostById($oTopic->getPostId()));
			$oTopic->setLastPost($this->PluginForum_ModulePost_GetPostById($oTopic->getLastPostId()));
			$oTopic->setLastUser($this->PluginForum_ModulePost_GetUserByPostId($oTopic->getLastPostId()));
			if (isset($aTopicsRead[$oTopic->getId()]))	{		
				$oTopic->setDateRead($aTopicsRead[$oTopic->getId()]->getDateRead());
			} else {
				$oTopic->setDateRead(date("Y-m-d H:i:s"));
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
	
	public function GetTopicsByForumId($Id,$iPage,$iPerPage) {
		if (false === ($data = $this->Cache_Get("topic_{$Id}_{$iPage}_{$iPerPage}"))) {			
			$data = array(
				'collection'=>$this->oMapperTopic->GetTopicsByForumId($Id,$iCount,$iPage,$iPerPage),
				'count'=>$iCount
				);
			$this->Cache_Set($data, "topic_{$Id}_{$iPage}_{$iPerPage}", array('topic_update','topic_new'), 60*60*24*2);
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
	
	public function GetTopicsByForumsArray($aId) {
		if (!$aId) {
			return null;
		}
		if (false === ($data = $this->Cache_Get("topic_{$aId}"))) {			
			$data = array('collection'=>$this->oMapperTopic->GetTopicsByForumsArray($aId));
			$this->Cache_Set($data, "topic_{$aId}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetTopicsByArrayId($data['collection']);
		return $data;
	}
	
	/**
	 * Обновляем/устанавливаем дату прочтения топика, если читаем его первый раз то добавляем
	 *
	 * @param ModuleTopic_EntityTopicRead $oTopicRead	 
	 */
	public function SetTopicRead(PluginForum_ModuleTopic_EntityTopicRead $oTopicRead) {		
		if ($this->GetTopicRead($oTopicRead->getTopicId(),$oTopicRead->getUserId())) {
			$this->Cache_Delete("topic_read_{$oTopicRead->getTopicId()}_{$oTopicRead->getUserId()}");
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("topic_read_user_{$oTopicRead->getUserId()}"));
			$this->oMapperTopic->UpdateTopicRead($oTopicRead);
		} else {
			$this->Cache_Delete("topic_read_{$oTopicRead->getTopicId()}_{$oTopicRead->getUserId()}");
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("topic_read_user_{$oTopicRead->getUserId()}"));
			$this->oMapperTopic->AddTopicRead($oTopicRead);
		}
		return true;		
	}
	/**
	 * Получаем дату прочтения топика юзером
	 *
	 * @param unknown_type $sTopicId
	 * @param unknown_type $sUserId
	 * @return unknown
	 */
	public function GetTopicRead($sTopicId,$sUserId) {
		$data=$this->GetTopicsReadByArray($sTopicId,$sUserId);
		if (isset($data[$sTopicId])) {
			return $data[$sTopicId];
		}
		return null;
	}
	/**
	 * Удаляет записи о чтении записей по списку идентификаторов
	 *
	 * @param  array|int $aTopicId
	 * @return bool
	 */
	public function DeleteTopicReadByArrayId($aTopicId) {
		if(!is_array($aTopicId)) $aTopicId = array($aTopicId);
		return $this->oMapperTopic->DeleteTopicReadByArrayId($aTopicId);
	}
	/**
	 * Получить список просмотром/чтения топиков по списку айдишников
	 *
	 * @param unknown_type $aTopicId
	 */
	public function GetTopicsReadByArray($aTopicId,$sUserId) {
		if (!$aTopicId) {
			return array();
		}
		if (Config::Get('sys.cache.solid')) {
			return $this->GetTopicsReadByArraySolid($aTopicId,$sUserId);
		}
		if (!is_array($aTopicId)) {
			$aTopicId=array($aTopicId);
		}
		$aTopicId=array_unique($aTopicId);
		$aTopicsRead=array();
		$aTopicIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aTopicId,'topic_read_','_'.$sUserId);
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {			
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {	
					if ($data[$sKey]) {
						$aTopicsRead[$data[$sKey]->getTopicId()]=$data[$sKey];
					} else {
						$aTopicIdNotNeedQuery[]=$sValue;
					}
				} 
			}
		}
		/**
		 * Смотрим каких топиков не было в кеше и делаем запрос в БД
		 */		
		$aTopicIdNeedQuery=array_diff($aTopicId,array_keys($aTopicsRead));		
		$aTopicIdNeedQuery=array_diff($aTopicIdNeedQuery,$aTopicIdNotNeedQuery);		
		$aTopicIdNeedStore=$aTopicIdNeedQuery;
		if ($data = $this->oMapperTopic->GetTopicsReadByArray($aTopicIdNeedQuery,$sUserId)) {
			foreach ($data as $oTopicRead) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aTopicsRead[$oTopicRead->getTopicId()]=$oTopicRead;
				$this->Cache_Set($oTopicRead, "topic_read_{$oTopicRead->getTopicId()}_{$oTopicRead->getUserId()}", array(), 60*60*24*4);
				$aTopicIdNeedStore=array_diff($aTopicIdNeedStore,array($oTopicRead->getTopicId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aTopicIdNeedStore as $sId) {
			$this->Cache_Set(null, "topic_read_{$sId}_{$sUserId}", array(), 60*60*24*4);
		}		
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aTopicsRead=func_array_sort_by_keys($aTopicsRead,$aTopicId);
		return $aTopicsRead;		
	}
	/**
	 * Получить список просмотром/чтения топиков по списку айдишников, но используя единый кеш
	 *
	 * @param unknown_type $aTopicId
	 * @param unknown_type $sUserId
	 * @return unknown
	 */
	public function GetTopicsReadByArraySolid($aTopicId,$sUserId) {
		if (!is_array($aTopicId)) {
			$aTopicId=array($aTopicId);
		}
		$aTopicId=array_unique($aTopicId);	
		$aTopicsRead=array();	
		$s=join(',',$aTopicId);
		if (false === ($data = $this->Cache_Get("topic_read_{$sUserId}_id_{$s}"))) {			
			$data = $this->oMapperTopic->GetTopicsReadByArray($aTopicId,$sUserId);
			foreach ($data as $oTopicRead) {
				$aTopicsRead[$oTopicRead->getTopicId()]=$oTopicRead;
			}
			$this->Cache_Set($aTopicsRead, "topic_read_{$sUserId}_id_{$s}", array("topic_read_user_{$sUserId}"), 60*60*24*1);
			return $aTopicsRead;
		}		
		return $data;
	}




	public function SetPostId($Id, $tId) {
		return $this->oMapperTopic->SetPostId($Id, $tId);
	}
	
	public function SetLastPostId($Id, $tId) {
		return $this->oMapperTopic->SetLastPostId($Id, $tId);
	}
	
	public function SetCountViews($iCount,$Id) {
		return $this->oMapperTopic->SetCountViews($iCount,$Id);
	}
	
	public function SetCountPosts($iCount,$Id) {
		return $this->oMapperTopic->SetCountPosts($iCount,$Id);
	}
	
	public function GetCountTopics() {
		return $this->oMapperTopic->GetCountTopics();
	}

}
?>