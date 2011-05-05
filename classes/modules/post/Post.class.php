<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/
 
class PluginForum_ModulePost extends Module {
	/**
	 * @var Mapper
	 */
	protected $oMapperPost;	
	protected $oUserCurrent=null;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapperPost=Engine::GetMapper(__CLASS__);
		$this->oMapperPost->SetUserCurrent($this->User_GetUserCurrent());
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}
	
	public function AddPost(PluginForum_ModulePost_EntityPost $oPost) {
		if ($sId=$this->oMapperPost->AddPost($oPost)) {
			$oPost->setId($sId);
			//чистим зависимые кеши
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('forum_topic_new',"forum_topic_update_user_{$oPost->getUserId()}","forum_topic_new_forum_{$oPost->getTopicId()}"));						
			return $oPost;
		}
		return false;
	}
	
	public function GetPostsByArrayId($aPostId) {
		if (!$aPostId) {
			return array();
		}
		if (!is_array($aPostId)) {
			$aPostId=array($aPostId);
		}
		$aPostId=array_unique($aPostId);
		$aPosts=array();
		$aPostIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aPostId,'forum_topic_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {			
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {	
					if ($data[$sKey]) {
						$aPosts[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aPostIdNotNeedQuery[]=$sValue;
					}
				} 
			}
		}
		/**
		 * Смотрим каких блогов не было в кеше и делаем запрос в БД
		 */		
		$aBPostIdNeedQuery=array_diff($aPostId,array_keys($aPosts));		
		$aBPostIdNeedQuery=array_diff($aBPostIdNeedQuery,$aPostIdNotNeedQuery);		
		$aPostIdNeedStore=$aBPostIdNeedQuery;
		if ($data = $this->oMapperPost->GetPostsByArrayId($aBPostIdNeedQuery)) {
			foreach ($data as $oPost) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aPosts[$oPost->getId()]=$oPost;
				$this->Cache_Set($oPost, "forum_topic_{$oPost->getId()}", array(), 60*60*24*4);
				$aPostIdNeedStore=array_diff($aPostIdNeedStore,array($oPost->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aPostIdNeedStore as $sId) {
			$this->Cache_Set(null, "forum_topic_{$sId}", array(), 60*60*24*4);
		}		
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aPosts=func_array_sort_by_keys($aPosts,$aPostId);
		return $aPosts;		
	}

	public function GetPostById($aPostId) {
		$aPosts=$this->GetPostsByArrayId($aPostId);
		if (isset($aPosts[$aPostId])) {
			return $aPosts[$aPostId];
		}
		return null;
	}
	
	public function GetPostsByTopicId($Id) {
		if (false === ($data = $this->Cache_Get("topic_{$Id}"))) {			
			$data = array('collection'=>$this->oMapperPost->GetPostsByTopicId($Id));
			$this->Cache_Set($data, "topic_{$Id}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetPostsAdditionalData($data['collection']);
		return $data;
	}
	
	public function GetPostsAdditionalData($aPostId,$aAllowData=array('user'=>array())) {
		func_array_simpleflip($aAllowData);
		if (!is_array($aPostId)) {
			$aPostId=array($aPostId);
		}
		/**
		 * Получаем комменты
		 */
		$aPosts=$this->GetPostsByArrayId($aPostId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();	
		$aTargetId=array('topic'=>array(),'talk'=>array());	
		foreach ($aPosts as $oPost) {
			if (isset($aAllowData['user'])) {
				$aUserId[]=$oPost->getUserId();
			}	
		}
		
		/**
		 * Получаем дополнительные данные
		 */
		$aUsers=isset($aAllowData['user']) && is_array($aAllowData['user']) ? $this->User_GetUsersAdditionalData($aUserId,$aAllowData['user']) : $this->User_GetUsersAdditionalData($aUserId);

		require_once('/home/www/artemeff.ru/my/FirePHPCore/FirePHP.class.php');
		$firephp = FirePHP::getInstance(true);
		$firephp -> fb($aPosts,FirePHP::LOG);
		
		$firephp -> fb($aUsers,FirePHP::LOG);
		
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aPosts as $oPost) {
			if (isset($aUsers[$oPost->getUserId()])) {
				$oPost->setUser($aUsers[$oPost->getUserId()]);
			} else {
				$oPost->setUser(null); // или $oPost->setUser(new ModuleUser_EntityUser());
			}				
		}
		
		return $aPosts;
	}

}
?>