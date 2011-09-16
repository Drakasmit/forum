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
	protected $oUserAdmin=false;

	/**
	 * Инициализация экшена
	 */
	public function Init() {
		$this->SetDefaultEvent('forums');
		$this->oUserCurrent=$this->User_GetUserCurrent();

		if ($this->User_IsAuthorization() or $oUserCurrent=$this->User_GetUserCurrent()) {
			if ($this->oUserCurrent->isAdministrator()) {
				$this->oUserAdmin=true;
			}
		}
	}
	
	/**
	 *	Регистрация эвентов
	 */
	protected function RegisterEvent() {
		// Админка
		$this->AddEvent('admin','EventAdmin');
		// Обработчики ajax запросов
		$this->AddEvent('ajaxaddpost','EventAddPost');
		$this->AddEvent('ajaxresponsepost','EventResponsePost');
		// Пользовательская часть
		$this->AddEvent('forums','EventForums');
		$this->AddEventPreg('/^add$/i','/^(\d+)$/i','EventAddTopic');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(page(\d+))?$/i','EventShowForum');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(\d+)-(.*)\.html$/i','/^(page(\d+))?$/i','EventShowTopic');
	}

	/**
	 *	Главная страница
	 */
	protected function EventForums() {
		/**
		 *	Получаем категории
		 */
        $aCategories=$this->PluginForum_ModuleCategory_GetCategories();
		/**
		 *	Задаем листинг категория-форум
		 */
        $aList=array();
        foreach ($aCategories as $oCategory) {
			$aResult=$this->PluginForum_ModuleForum_GetForumsByCategoryId($oCategory->getId());
			$aForums=$aResult['collection'];
            $aList[]=array(
                    'obj'=>$oCategory,
                    'forums'=>$aForums
            );
        }
		/**
		 *	Получаем статистику
		 */
		$this->GetForumStats();
		/**
		 *	Передаем переменную в шаблон
		 */
		$this->Viewer_Assign('aCategories',$aList);
		/**
		 *	Задаем title
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('main_title'));
		/**
		 *	Задаем шаблон
		 */
		$this->SetTemplateAction('index');	
	}
	
	/**
	 *	Показ ветки форума
	 */
	protected function EventShowForum() {
		/**
		 * Получаем URL форума из эвента
		 */
		$sUrl=$this->sCurrentEvent;
		/**
		 * Объект форума
		 */
		if(!($oForum=$this->PluginForum_ModuleForum_GetForumByUrl($sUrl))) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем страницу
		 */
		if(!($iPage=$this->GetParamEventMatch(0,2))) $iPage=1;
		/**
		 * Получаем топики
		 */
		$aResult=$this->PluginForum_ModuleTopic_GetTopicsByForumId($oForum->getId(),$iPage,Config::Get('plugin.forum.topics.per_page'));
		$aTopics=$aResult['collection'];
		/**
		 *	Постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.topics.per_page'),4,Router::GetPath('forum').$oForum->getUrl());
		/**
		 *	Тайтлы
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('main_title'));
		$this->Viewer_AddHtmlTitle($oForum->getTitle());
		/**
		 *	Передаем переменные в шаблон
		 */
		$this->Viewer_Assign("aTopics",$aTopics);
		$this->Viewer_Assign("aPaging",$aPaging);
		$this->Viewer_Assign("oForum",$oForum);
		/**
		 *	Задаем шаблон
		 */
		$this->SetTemplateAction('forum');	
	}
	
	/**
	 *	Просмотр топика
	 */
	protected function EventShowTopic() {
		/**
		 * Получаем URL форума из эвента
		 */
		$sForumUrl=$this->sCurrentEvent;
		/**
		 * Получаем сущность форума из URL
		 */
		$oForum=$this->PluginForum_ModuleForum_GetForumByUrl($sForumUrl);
		/**
		 * Получаем ID топика из URL
		 */
		$sId=$this->GetParamEventMatch(0,1);
		/**
		 * Сущность из ID
		 */
		$oTopic=$this->PluginForum_ModuleTopic_GetTopicById($sId);
		/**
		 * Получаем URL топика из евента
		 */
		$sTitle=$this->GetParamEventMatch(0,2);
		/**
		 * Если они не совпадают, редиректим на валидный УРЛ
		 */
		if ($sTitle!=$oTopic->getUrl()) {
			header('Location: '.Router::GetPath('forum').$sForumUrl.'/'.$sId.'-'.$oTopic->getUrl().'.html');
		}
		/**
		 * Хука для счетчиков
		 */
		$this->Hook_Run('forum_topic_show',array("oTopic"=>$oTopic));
		/**
		 * Получаем номер страницы
		 */
		if(!($iPage=$this->GetParamEventMatch(1,2))) $iPage=1;
		/**
		 * Получаем топики
		 */
		$aResult=$this->PluginForum_ModulePost_GetPostsByTopicId($oTopic->getId(),$iPage,Config::Get('plugin.forum.posts.per_page'));
		$aPost=$aResult['collection'];
		$iMaxIdPost=$aResult['lastPostId'];	
		/**
		 * Пагинация
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.posts.per_page'),4,Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');
		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			$oTopicRead=Engine::GetEntity('PluginForum_Topic_TopicRead');
			$oTopicRead->setTopicId($oTopic->getId());
			$oTopicRead->setUserId($this->oUserCurrent->getId());
			$oTopicRead->setPostIdLast($oTopic->getLastPostId());
			$oTopicRead->setDateRead(date("Y-m-d H:i:s"));
			$this->PluginForum_ModuleTopic_SetTopicRead($oTopicRead);
		}
		/**
		 * Теперь все в шаблон
		 */
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_Assign("aPost",$aPost);
		$this->Viewer_Assign("oTopic",$oTopic);
		$this->Viewer_Assign("aPaging",$aPaging);
		$this->Viewer_Assign("iMaxIdPost",$iMaxIdPost);
		/**
		 *	Загаловки
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('main_title'));
		$this->Viewer_AddHtmlTitle($oForum->getTitle());
		$this->Viewer_AddHtmlTitle($oTopic->getTitle());
		/**
		 *	Задаем шаблон
		 */
		$this->SetTemplateAction('topic');
	}
	
	/**
	 *	Добавление топика
	 */
	public function EventAddTopic() {
		if (!$this->User_IsAuthorization()) {
			return parent::EventNotFound();
		}
		$sForumId = $this->GetParamEventMatch(0,1);
		$oForum = $this->PluginForum_ModuleForum_GetForumById($sForumId);
		
		$this->Viewer_Assign("oForum",$oForum);
		return $this->SubmitAdd();
	}
	
	/**
	 *	Сабмит формы добавления топика
	 */
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
		$sForumId=$this->GetParamEventMatch(0,1);
		$oForum=$this->PluginForum_ModuleForum_GetForumById($sForumId);
		/**
		 * Если форум не определен выдаем предупреждение
		 */
		if (!$oForum) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_unknown'),$this->Lang_Get('error'));
			return false;
		}		
		/**
		 * Проверяем права на постинг в форум
		 * Скоро будут права на молчание в определенных форумах

		if (!$this->ACL_IsAllowBlog($oBlog,$this->oUserCurrent)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_noallow'),$this->Lang_Get('error'));
			return false;
		}
		*/

		/**
		 * Теперь можно смело добавлять топик к форуму
		 */
		$oTopic=Engine::GetEntity('PluginForum_Topic');
		$oTopic->setForumId($oForum->getId());
		$oTopic->setUserId($this->oUserCurrent->getId());
		$oTopic->setTitle(getRequest('topic_title'));
		$oTopic->setDate(date("Y-m-d H:i:s"));
		$oTopic->setUrl($this->PluginForum_ModuleTopic_GenerateUrl(getRequest('topic_title')));
		$oTopic->setCountViews('0');
		$oTopic->setCountPosts('0');
		
		/**
		 *	Статус:
		 *	0 - открыт
		 * 1 - закрыт
		 */
		$oTopic->setStatus(0);
		if ($this->oUserCurrent->isAdministrator())	{
			if (getRequest('topic_status')) {
				$oTopic->setStatus(1);
			}
		}
		
		/**
		 *	Позиция в ветке
		 *	0 - обычно
		 * 1- прикреплен
		 */
		$oTopic->setPosition(0);
		if ($this->oUserCurrent->isAdministrator())	{
			if (getRequest('topic_position')) {
				$oTopic->setPosition(1);
			} 
		}	

		
		$oPost=Engine::GetEntity('PluginForum_Post');
		$oPost->setUserId($this->oUserCurrent->getId());
		$oPost->setDate(date("Y-m-d H:i:s"));
		$oPost->setForumId($oForum->getId());
		
		$oPost->setText($this->Text_Parser(getRequest('topic_text')));
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
				$this->PluginForum_ModuleTopic_SetPostId($oPost->getId(),$oTopic->getId());
				$this->PluginForum_ModuleForum_UpdateForumLatestData($oPost->getId(),$oTopic->getId(),$this->oUserCurrent->getId(),$oForum->getId());
				$this->PluginForum_ModuleTopic_SetLastPostId($oPost->getId(),$oTopic->getId());
				
				header('Location: '.Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');
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
	
	/**
	 *	Добавление поста
	 */
	protected function EventAddPost() {
		/**
		 *	Проверка формы
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверка авторизации пользователя
		 */
		if ($this->User_IsAuthorization()) {
			$this->oUserCurrent=$this->User_GetUserCurrent();
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;	
		}
		/**
		 * Сущность форума
		 */
		if (!($oForum=$this->PluginForum_ModuleForum_GetForumById(getRequest('forum_id')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Сущность топика
		 */
		if (!($oTopic=$this->PluginForum_ModuleTopic_GetTopicById(getRequest('topic_id')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем запрет на добавления коммента автором топика или топик закрытый
		*/
		if ($oTopic->getStatus()==1 AND !$this->oUserAdmin) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем текст поста
		*/
		$sText=$this->Text_Parser(getRequest('form_post_text'));
		if (!func_check($sText,'text',2,10000)) {			
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Создаём
		*/
		$oPost=Engine::GetEntity('PluginForum_Post');
		$oPost->setForumId($oForum->getId());
		$oPost->setTopicId($oTopic->getId());
		$oPost->setUserId($this->oUserCurrent->getId());		
		$oPost->setText($sText);
		$oPost->setTextSource(getRequest('form_post_text'));
		$oPost->setDate(date("Y-m-d H:i:s"));
		/**
		* Добавляем
		*/
		if ($this->PluginForum_ModulePost_AddPost($oPost)) {
			$this->PluginForum_ModuleTopic_SetLastPostId($oPost->getId(),$oTopic->getId());
			$this->PluginForum_ModuleForum_UpdateForumLatestData($oPost->getId(),$oTopic->getId(),$this->oUserCurrent->getId(),$oForum->getId());
			
			$this->Viewer_AssignAjax('idPostLast',getRequest('last_post'));
			$this->Viewer_AssignAjax('idForum',$oForum->getId());
			
			$this->Message_AddNoticeSingle('notice','notice');
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}

	}

	/**
	 *	Получение новых постов
	 */
	public function EventResponsePost() {
		/**
		 *	Проверка запроса
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 *	Проверка авторизации
		 */
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Сущность форума
		 */
		if (!($oForum=$this->PluginForum_ModuleForum_GetForumById(getRequest('idTargetForum')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Сущность топика
		 */
		if (!($oTopic=$this->PluginForum_ModuleTopic_GetTopicById(getRequest('idTargetTopic')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$idPostLast=getRequest('idPostLast',null,'post');

		$aPosts=array();
		$aReturn=$this->PluginForum_ModulePost_GetNewPostsByTopicId($oTopic->getId(),$idPostLast);
		$iMaxIdPost=$aReturn['iMaxIdPost'];

		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			$oTopicRead=Engine::GetEntity('PluginForum_Topic_TopicRead');
			$oTopicRead->setTopicId($oTopic->getId());
			$oTopicRead->setUserId($this->oUserCurrent->getId());
			$oTopicRead->setPostIdLast($oTopic->getLastPostId());
			$oTopicRead->setDateRead(date("Y-m-d H:i:s"));
			$this->PluginForum_ModuleTopic_SetTopicRead($oTopicRead);
		}

		$aPsts=$aReturn['posts'];
		if ($aPsts and is_array($aPsts)) {
			foreach ($aPsts as $aPst) {
				$aPosts[]=array(
					'html' => $aPst['html'],
					'idParent' => $aPst['obj']->getPid(),
					'id' => $aPst['obj']->getId(),
				);
			}
		}

		$this->Viewer_AssignAjax('iMaxIdPost',$iMaxIdPost);
		$this->Viewer_AssignAjax('aPosts',$aPosts);
	}

    /**
     * Получение REQUEST-переменной с проверкой "ключа секретности"
     *
     * @param <type> $sName
     * @param <type> $default
     * @param <type> $sType
     * @return <type>
     */
    protected function GetRequestCheck($sName, $default=null, $sType=null) {
        $result=getRequest($sName, $default, $sType);

        if (!is_null($result)) $this->Security_ValidateSendForm();

        return $result;
    }

    /**
     * Проверяем существует ли Категория, если да то удаляем
     */
	protected function EventCategoryDelete() {
        if (!$this->oUserAdmin) {
			return parent::EventNotFound(); 
		}
		$iCategoryId=$this->GetRequestCheck('cat_id');
		if ($iCategoryId && ($oCategory=$this->PluginForum_ModuleCategory_GetCategoryById($iCategoryId))) {
			$this->PluginForum_ModuleCategory_DeleteCategory($iCategoryId);
		}
        header('Location: '.Router::GetPath('forum').'admin/categories/');
	}
	
	/**
	 *	Фкнция для получения статистики активности форума
	 */
	protected function GetForumStats() {
		/**
		 * Статистика
		 */
		$aForumStat=$this->PluginForum_ModuleForum_GetStatForums();		
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForumStat',$aForumStat);
	}
	
	/**
	 *	Админка, пока только заготовка
	 */
	protected function EventAdmin() {
		if (!$this->oUserAdmin) {
			return parent::EventNotFound();
		}
		
		if ($this->GetParam(0)=='categories') {
		
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

			$this->Viewer_Assign('aCategories', $aList);
		
			$this->SetTemplateAction('admin-categories');
		}
		
		if ($this->GetParam(0)=='forums') {
			$this->SetTemplateAction('admin-forums');
		}
		
		if ($this->GetParam(0)=='topics') {
			$this->SetTemplateAction('admin-topics');
		}
		
		if (!$this->GetParam(0)) {
			$this->SetTemplateAction('admin');
		}
		
		if ($this->GetParam(0)=='categories' and $this->GetParam(1)=='delete') {
			$this->EventCategoryDelete();
		}
		
	}

	/**
	 * Завершение работы экшена
	 */
	public function EventShutdown() {
		$sTemplatePath=Plugin::GetTemplatePath(__CLASS__);
		$sTemplateWebPath=Plugin::GetTemplateWebPath(__CLASS__);
		$this->Viewer_Assign('sTP',$sTemplatePath);
		$this->Viewer_Assign('sTWP',$sTemplateWebPath);
	}
}
?>