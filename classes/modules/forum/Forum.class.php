<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/
 
class PluginForum_ModuleForum extends Module {
	/**
	 * @var Mapper
	 */
	protected $oMapperForum;	
	protected $oUserCurrent=null;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapperForum=Engine::GetMapper(__CLASS__);
		$this->oMapperForum->SetUserCurrent($this->User_GetUserCurrent());
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}
	
	/**
	 * Список форумов по ID
	 *
	 * @param array $aUserId
	 */
	public function GetForumsByArrayId($aForumId) {
		if (!$aForumId) {
			return array();
		}
		if (!is_array($aForumId)) {
			$aForumId=array($aForumId);
		}
		$aForumId=array_unique($aForumId);
		$aForums=array();
		$aForumIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aForumId,'forum_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {			
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {	
					if ($data[$sKey]) {
						$aForums[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aForumIdNotNeedQuery[]=$sValue;
					}
				} 
			}
		}
		/**
		 * Смотрим каких блогов не было в кеше и делаем запрос в БД
		 */		
		$aBForumIdNeedQuery=array_diff($aForumId,array_keys($aForums));		
		$aBForumIdNeedQuery=array_diff($aBForumIdNeedQuery,$aForumIdNotNeedQuery);		
		$aForumIdNeedStore=$aBForumIdNeedQuery;
		if ($data = $this->oMapperForum->GetForumsByArrayId($aBForumIdNeedQuery)) {
			foreach ($data as $oForum) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aForums[$oForum->getId()]=$oForum;
				$this->Cache_Set($oForum, "forum_{$oForum->getId()}", array(), 60*60*24*4);
				$aForumIdNeedStore=array_diff($aForumIdNeedStore,array($oForum->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aForumIdNeedStore as $sId) {
			$this->Cache_Set(null, "forum_{$sId}", array(), 60*60*24*4);
		}		
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aForums=func_array_sort_by_keys($aForums,$aForumId);
		return $aForums;		
	}
	
	
	public function GetForumsAdditionalData($aForumId,$aAllowData=array('topic'=>array())) {
		func_array_simpleflip($aAllowData);
		if (!is_array($aForumId)) {
			$aForumId=array($aForumId);
		}
		/**
		 * Получаем комменты
		 */
		$aForums=$this->GetForumsByArrayId($aForumId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aTopicId=array();
		foreach ($aForums as $oForum) {
			if (isset($aAllowData['topic'])) {
				$aTopicId[]=$oForum->getId();
			}	
		}
		/**
		 * Получаем дополнительные данные
		 */
		$aTopics=isset($aAllowData['topic']) && is_array($aAllowData['topic']) ? $this->PluginForum_ModuleTopic_GetTopicsByForumsArray($aTopicId,$aAllowData['topic']) : $this->PluginForum_ModuleTopic_GetTopicsByForumsArray($aTopicId);
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aForums as $oForum) {
			if (isset($aTopics[$oForum->getLastTopic()])) {
				$oForum->setTopic($aTopics[$oForum->getLastTopic()]);
			} else {
				$oForum->setTopic(null); // или $oComment->setUser(new ModuleUser_EntityUser());
			}				
		}
		return $aForums;
	}

	
	/**
	 * Получаем ID форумов
	 *
	 */

	public function GetForums($bReturnIdOnly=false) {
		$data=$this->oMapperForum->GetForums();
		/**
		 * Возвращаем только иденитификаторы
		 */
		if($bReturnIdOnly) return $data;

		$data=$this->GetForumsByArrayId($data);
		return $data;
	}

	public function GetForumById($sForumId) {
		$aForums=$this->GetForumsByArrayId($sForumId);
		if (isset($aForums[$sForumId])) {
			return $aForums[$sForumId];
		}
		return null;
	}

	public function GetForumByUrl($sUrl) {
		if (false === ($id = $this->Cache_Get("forum_url_{$sUrl}"))) {
			if ($id = $this->oMapperForum->GetForumByUrl($sUrl)) {
				$this->Cache_Set($id, "forum_url_{$sUrl}", array("forum_update_{$id}"), 60*60*24*2);
			} else {
				$this->Cache_Set(null, "forum_url_{$sUrl}", array('forum_update_','forum_new'), 60*60);
			}
		}
		return $this->GetForumById($id);
	}

	public function GetForumsByCategoryId($Id) {
		if (false === ($data = $this->Cache_Get("forum_cat_{$Id}"))) {			
			$data = array('collection'=>$this->oMapperForum->GetForumsByCategoryId($Id));
			$this->Cache_Set($data, "forum_cat_{$Id}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetForumsAdditionalData($data['collection']);
		return $data;		
	}

}
?>