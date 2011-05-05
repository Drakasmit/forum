<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ActionForum extends ActionPlugin {
	/**
	 * Текущий юзер
	 *
	 * @var ModuleUser_EntityUser
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 * @return null
	 */
	public function Init() {
		$this->SetDefaultEvent('forums');
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}

	protected function RegisterEvent() {
		$this->AddEvent('forums','EventForums');
		$this->AddEvent('ajaxaddcomment','AjaxAddComment');

		$this->AddEventPreg('/^add$/i','/^(\d+)$/i','EventAddTopic');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(page(\d+))?$/i','EventShowForum');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(\d+)-(\w+)\.html$/i','EventShowTopic');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	protected function EventForums() {
		/**
		 * Получаем список форумов
		 */
        $aCategories=$this->PluginForum_ModuleCategory_GetCategories();
        $aList = array();
        foreach ($aCategories as $oCategory) {
			$aResult=$this->PluginForum_ModuleForum_GetForumsByCategoryId($oCategory->getId());
			$aForums=$aResult['collection'];
            $aList[] = array(
                    'obj'=>$oCategory,
                    'forums'=>$aForums
            );
        }
		
		//$this->Viewer_Assign("aForums",$aForums);
		$this->Viewer_Assign('aCategories', $aList);
		
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');	
		
		//$aResult=$this->PluginForum_ModuleForum_GetForumsByCategoryId('2');
		//$aForums=$aResult['collection'];
	}
	
	protected function EventShowForum() {
	
		$sUrl=$this->sCurrentEvent;
		
		$oForum = $this->PluginForum_ModuleForum_GetForumByUrl($sUrl);
		
		$aResult = $this->PluginForum_ModuleTopic_GetTopicsByForumId($oForum->getId());
		$aTopics = $aResult['collection'];
		
		$this->Viewer_Assign("aTopics",$aTopics);
		$this->Viewer_Assign("oForum",$oForum);
		$this->SetTemplateAction('forum');	
	}
	
	protected function EventShowTopic() {
		// Получаем УРЛ форума, для редиректа
		$sForumUrl=$this->sCurrentEvent;
		// Сущность форума из его УРЛа
		$oForum = $this->PluginForum_ModuleForum_GetForumByUrl($sForumUrl);
		// Получаем УРЛ топика из ссылки
		$sUrl=$this->GetParamEventMatch(0,1);
		// Сущность топика
		$oTopic = $this->PluginForum_ModuleTopic_GetTopicById($sUrl);
		// Получаем из сущности УРЛ топика, для сравнения
		$oTopicReallyUrl = $oTopic->getUrl();
		// Получаем УРЛ топика, для сравнения
		$sTitle=$this->GetParamEventMatch(0,2);
		// Если они не совпадают, редиректим на валидный УРЛ
		if ($sTitle != $oTopicReallyUrl) {
			header('Location: http://my.artemeff.ru/forum/'.$sForumUrl.'/'.$sUrl.'-'.$oTopicReallyUrl.'.html');
		}
		
		$aResult = $this->PluginForum_ModulePost_GetPostsByTopicId($oTopic->getId());
		$aPost = $aResult['collection'];
		
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_Assign("aPost",$aPost);
		$this->Viewer_Assign("oTopic",$oTopic);
		$this->SetTemplateAction('topic');
	}
	
	public function EventAddTopic() {
		if (!$this->User_IsAuthorization()) {
			return parent::EventNotFound();
		}
		$sForumId = $this->GetParamEventMatch(0,1);
		$oForum = $this->PluginForum_ModuleForum_GetForumById($sForumId);
		
		$this->Viewer_Assign("oForum",$oForum);
		return $this->SubmitAdd();
	}
	
	protected function SubmitAdd() {
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */		
		if (!isPost('submit_topic_publish')) {
			return false;
		}	
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkTopicFields()) {
			return false;	
		}		
		/**
		 * Определяем в какой форум делаем запись
		 */
		$sForumId = $this->GetParamEventMatch(0,1);
		$oForum = $this->PluginForum_ModuleForum_GetForumById($sForumId);
		/**
		 * Если форум не определен выдаем предупреждение
		 */
		if (!$oForum) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_unknown'),$this->Lang_Get('error'));
			return false;
		}		
		/**
		 * Проверяем права на постинг в форум
		 */
		/*if (!$this->ACL_IsAllowBlog($oBlog,$this->oUserCurrent)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_noallow'),$this->Lang_Get('error'));
			return false;
		}				*/	
		/**
		 * Проверяем разрешено ли постить топик по времени
		 */
		/*if (isPost('submit_topic_publish') and !$this->ACL_CanPostTopicTime($this->oUserCurrent)) {			
			$this->Message_AddErrorSingle($this->Lang_Get('topic_time_limit'),$this->Lang_Get('error'));
			return;
		}*/
		/**
		 * Теперь можно смело добавлять топик к форуму
		 */
		$oTopic=Engine::GetEntity('PluginForum_Topic');
		$oTopic->setForumId($oForum->getId());
		$oTopic->setUserId($this->oUserCurrent->getId());
		$oTopic->setTitle(getRequest('topic_title'));
		$oTopic->setDate(date("Y-m-d H:i:s"));
		$oTopic->setUrl('test');
		$oTopic->setStatus('1');
		
		
		$oPost=Engine::GetEntity('PluginForum_Post');
		$oPost->setUserId($this->oUserCurrent->getId());
		$oPost->setDate(date("Y-m-d H:i:s"));
		
		$oPost->setText(getRequest('topic_text'));
		$oPost->setTextSource(getRequest('topic_text'));
		
		/**
		 * Добавляем топик
		 */		
		if ($this->PluginForum_ModuleTopic_AddTopic($oTopic)) {
			/**
			 * Получаем топик, чтоб подцепить связанные данные
			 */
			$oTopic=$this->PluginForum_ModuleTopic_GetTopicById($oTopic->getId());
			$oPost->setTopicId($oTopic->getId());
			
			/**
			 * Добавляет первый пост
			 */
			if ($this->PluginForum_ModulePost_AddPost($oPost)) {
				/**
				 * Получаем пост, чтоб подцепить связанные данные
				 */
				$oPost=$this->PluginForum_ModulePost_GetPostById($oPost->getId());
				//Router::Location($oTopic->getUrl());
				header('Location: http://my.artemeff.ru/forum/'.$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
				return Router::Action('error');
			}
			
						
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
	}
	
	/**
	 * Проверка полей формы
	 *
	 * @return unknown
	 */
	protected function checkTopicFields() {
		$this->Security_ValidateSendForm();
		
		$bOk=true;
		/**
		 * Проверяем есть ли заголовок топика
		 */
		if (!func_check(getRequest('topic_title',null,'post'),'text',2,200)) {
			$this->Message_AddError($this->Lang_Get('topic_create_title_error'),$this->Lang_Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем есть ли содержание топика
		 */
		if (!func_check(getRequest('topic_text',null,'post'),'text',2,Config::Get('module.topic.max_length'))) {
			$this->Message_AddError($this->Lang_Get('topic_create_text_error'),$this->Lang_Get('error'));
			$bOk=false;
		}
		/**
		 * Выполнение хуков
		 */
		$this->Hook_Run('check_topic_fields', array('bOk'=>&$bOk));
		
		return $bOk;
	}
	
	
	protected function AjaxAddComment() {
		$this->Viewer_SetResponseAjax();
		$this->SubmitComment();
	}	
	/**
	 * Обработка добавление поста к топику
	 *	 
	 * @return bool
	 */
	protected function SubmitComment() {
		/**
		 * Проверям авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		// Сущность топика
		$oTopic = $this->PluginForum_ModuleTopic_GetTopicById(getRequest('post_topic_id'));
		
		/**
		* Проверяем разрешено ли постить
		*/
		if (!$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {			
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_acl'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем разрешено ли постить по времени
		*/
		if (!$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {			
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_limit'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем запрет на добавления коммента автором топика или топик закрытый
		*/
		/*if ($oTopic->getForbidComment()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
			return;
		}	*/
		/**
		* Проверяем текст поста
		*/
		$sText=getRequest('comment_text');
		if (!func_check($sText,'text',2,10000)) {			
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Создаём
		*/
		$oPost=Engine::GetEntity('PluginForum_Post');
		$oPost->setTopicId($oTopic->getId());
		$oPost->setUserId($this->oUserCurrent->getId());		
		$oPost->setText($sText);
		$oPost->setTextSource($sText);
		$oPost->setDate(date("Y-m-d H:i:s"));
			
		/**
		* Добавляем
		*/
		
		if ($this->PluginForum_ModulePost_AddPost($oPost)) {
			
			$this->Viewer_AssignAjax('sPostId',$oPost->getId());
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}
	}	

	/**
	 * Завершение работы Action`a
	 *
	 */
	public function EventShutdown() {

	}
}
?>