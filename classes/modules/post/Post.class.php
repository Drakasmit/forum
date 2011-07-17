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
			//чистим зависимые кеши
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('forum_topic_new',"forum_topic_{$oPost->getTopicId()}",'topic_update','topic_new'));						
			$oPost->setId($sId);
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
	
	public function GetPostsByTopicId($Id,$iPage,$iPerPage) {
		if (false === ($data = $this->Cache_Get("topic_{$Id}_{$iPage}_{$iPerPage}"))) {			
			$data = array(
				'collection'=>$this->oMapperPost->GetPostsByTopicId($Id,$iCount,$iPage,$iPerPage),
				'count'=>$iCount,
				'lastPostId'=>$this->oMapperPost->GetLastPostByTopicId($Id)
				);
			$this->Cache_Set($data, "topic_{$Id}_{$iPage}_{$iPerPage}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetPostsAdditionalData($data['collection']);
		return $data;
	}
	
	/*public function GetNewPostsByTopicId($Id,$idPostLast) {
		if (false === ($data = $this->Cache_Get("topic_{$Id}_{$idPostLast}"))) {			
			$data = array(
				'collection'=>$this->oMapperPost->GetNewPostsByTopicId($Id,$idPostLast),
				'lastPostId'=>$this->oMapperPost->GetLastPostByTopicId($Id)
				);
			$this->Cache_Set($data, "topic_{$Id}_{$idPostLast}", array('topic_update','topic_new'), 60*60*24*2);
		}
		$data['collection']=$this->GetPostsAdditionalData($data['collection']);
		return $data;
	}*/
	
	public function GetUserByPostId($Id) {
		if (false === ($id = $this->Cache_Get("post_user_{$Id}"))) {
			if ($id = $this->oMapperPost->GetUserByPostId($Id)) {
				$this->Cache_Set($id, "post_user_{$Id}", array("post_update_{$id}"), 60*60*24*2);
			} else {
				$this->Cache_Set(null, "post_user_{$Id}", array('post_update_','topic_new'), 60*60);
			}
		}
		return $this->User_GetUserById($id);
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
	
	public function GetNewPostsByTopicId($sId,$sIdPostLast) {
		if (false === ($aPosts = $this->Cache_Get("post_{$sId}_{$sIdPostLast}"))) {			
			$aPosts=$this->oMapperPost->GetNewPostsByTopicId($sId,$sIdPostLast);			
			$this->Cache_Set($aPosts, "post_{$sId}_{$sIdPostLast}", array("post_new_{$sId}"), 60*60*24*1);
		}		
		
		if (count($aPosts)==0) {
			return array('posts'=>array(),'iMaxIdPost'=>0);
		}
		
		$iMaxIdPost=max($aPosts);		
		$aPsts=$this->GetPostsAdditionalData($aPosts);
		if (!class_exists('ModuleViewer')) {
			require_once(Config::Get('path.root.engine')."/modules/viewer/Viewer.class.php");
		}
		$oViewerLocal=$this->Viewer_GetLocalViewer();
		$oViewerLocal->Assign('oUserCurrent',$this->User_GetUserCurrent());
		$oViewerLocal->Assign('bOneComment',true);

		$aPst=array();
		foreach ($aPsts as $oPost) {			
			$oViewerLocal->Assign('oPost',$oPost);
			$oViewerLocal->Assign('oTopic',$this->PluginForum_ModuleTopic_GetTopicById($oPost->getTopicId()));
			$oViewerLocal->Assign('oForum',$this->PluginForum_ModuleForum_GetForumById($oPost->getForumId()));
			$sText=$oViewerLocal->Fetch(Plugin::GetTemplatePath(__CLASS__).'/post.tpl');
			$aPst[]=array(
				'html' => $sText,
				'obj'  => $oPost,
			);			
		}
			
		return array('posts'=>$aPst,'iMaxIdPost'=>$iMaxIdPost);		
	}

	public function GetCountPosts() {
		return $this->oMapperPost->GetCountPosts();
	}

	public function GetCountToDayPosts() {
		$sDate=date("Y-m-d H:00:00",time()-60*60*24*1);
		if (false === ($data = $this->Cache_Get("post_count_{$sDate}"))) {			
			$data = $this->oMapperPost->GetCountToDayPosts($sDate);
			$this->Cache_Set($data, "post_count_{$sDate}", array('post_update','post_new'), 60*60*24*2);
		}
		return $data;
	}

}
?>