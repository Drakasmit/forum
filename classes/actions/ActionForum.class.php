<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
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
		/**
		 * Получаем текущего пользователя
		 */
		$this->oUserCurrent=$this->User_GetUserCurrent();

		if ($this->User_IsAuthorization() or $oUserCurrent=$this->User_GetUserCurrent()) {
			if ($this->oUserCurrent->isAdministrator()) {
				$this->oUserAdmin=true;
			}
		}

		/**
		 * Устанавливаем дефолтный эвент
		 */
		$this->SetDefaultEvent('forums');
	}

	/**
	 * Регистрация эвентов
	 */
	protected function RegisterEvent() {
		/**
		 * Админка
		 */
		$this->AddEvent('admin','EventAdmin');
		/**
		 * Обработчики ajax запросов
		 */
		$this->AddEvent('ajaxaddpost','EventAddPost');
		$this->AddEvent('ajaxresponsepost','EventResponsePost');
		/**
		 * Пользовательская часть
		 */
		$this->AddEvent('forums','EventForums');
		$this->AddEventpreg('/^unread$/i','/^(page(\d+))?$/i','EventUnread');
		$this->AddEventPreg('/^add$/i','/^(\d+)$/i','EventAddTopic');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(page(\d+))?$/i','EventShowForum');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(\d+)\-(.*).html$/i','/^(page(\d+))?$/i','EventShowTopic');
		$this->AddEventPreg('/^(\d+)$/i','/^(\d+)\-(.*).html$/i','/^(page(\d+))?$/i','EventShowTopic');
		$this->AddEventPreg('/^(\d+)$/i','/^(page(\d+))?$/i','EventShowForum');
	}


	/**
	 * Главная страница форума
	 *
	 */
	public function EventForums() {
		/**
		 * Получаем список форумов
		 */
		$aForums=$this->PluginForum_ModuleForum_LoadTree();
		/**
		 * Получаем статистику
		 */
		$this->GetForumStats();
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForums',$aForums);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('main_title'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');
	}


	/**
	 * Просмотр форума
	 */
	public function EventShowForum() {
		/**
		 * Получаем URL форума из эвента
		 */
		$sUrl=$this->sCurrentEvent;
		/**
		 * Получаем форум по URL
		 */
		if(!($oForum=$this->PluginForum_ModuleForum_GetForumByUrl($sUrl))) {
			/**
			 * Возможно форум не имеет собственного url
			 */
			if(!($oForum=$this->PluginForum_ModuleForum_GetForumById($sUrl))) {
				return parent::EventNotFound();
			}
		}
		/**
		 * Получаем текущую страницу
		 */
		$iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;	
		/**
		 * Получаем топики
		 */
		$aResult=$this->PluginForum_ModuleForum_GetTopicItemsByForumId($oForum->getId(),array('#order'=>array('topic_position'=>'desc', 'last_post_id'=>'desc', 'topic_date'=>'desc'),'#page' => array($iPage,Config::Get('plugin.forum.topics.per_page'))));
		$aTopics=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.topics.per_page'),4,Router::GetPath('forum').$oForum->getUrl());
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("aPaging",$aPaging);
		$this->Viewer_Assign("aTopics",$aTopics);
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('forums'));
		$this->Viewer_AddHtmlTitle($oForum->getTitle());
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('forum');
	}

	/**
	 * Просмотр топика
	 */
	public function EventShowTopic() {
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
		$oTopic=$this->PluginForum_ModuleForum_GetTopicById($sId);
		/**
		 * Получаем URL топика из евента
		 */
		$sUrl=$this->GetParamEventMatch(0,2);
		/**
		 * Если они не совпадают, редиректим на валидный УРЛ
		 */
		if ($sUrl!=$oTopic->getUrl()) {
			Router::Location(Router::GetPath('forum').$sForumUrl.'/'.$sId.'-'.$oTopic->getUrl().'.html');
		}
		/**
		 * Счетчик просмотров
		 */
		$oTopic->setViews($oTopic->getViews()+1);
		$oTopic->Update();
		/**
		 * Получаем номер страницы
		 */
		$iPage=$this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;	
		/**
		 * Получаем топики
		 */
		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByTopicId($oTopic->getId(), array('#page'=>array($iPage,Config::Get('plugin.forum.posts.per_page'))));
		$aPost=$aResult['collection'];
		$iMaxIdPost=$oTopic->getPostId();
		/**
		 * Пагинация
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.posts.per_page'),4,Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');
		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			if ($oRead=$this->PluginForum_ModuleForum_GetReadByTopicIdAndUserId($oTopic->getId(), $this->oUserCurrent->getId())) {
				$oRead->setPostId($oTopic->getLastPostId());
				$oRead->setDate(date("Y-m-d H:i:s"));
				$oRead->Update();
			} else {
				$oRead=LS::Ent('PluginForum_ModuleForum_EntityRead');
				$oRead->setTopicId($oTopic->getId());
				$oRead->setUserId($this->oUserCurrent->getId());
				$oRead->setPostId($oTopic->getLastPostId());
				$oRead->setDate(date("Y-m-d H:i:s"));
				$oRead->Add();
			}
			$oRead=$this->PluginForum_ModuleForum_GetReadByTopicIdAndUserId($oTopic->getId(), $this->oUserCurrent->getId());
		} else {
			$oRead=null;
		}
		/**
		 * Теперь все в шаблон
		 */
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_Assign("aPost",$aPost);
		$this->Viewer_Assign("oTopic",$oTopic);
		$this->Viewer_Assign("oRead",$oRead);
		$this->Viewer_Assign("aPaging",$aPaging);
		$this->Viewer_Assign("iMaxIdPost",$iMaxIdPost);
		/**
		 * Загаловки
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('main_title'));
		$this->Viewer_AddHtmlTitle($oForum->getTitle());
		$this->Viewer_AddHtmlTitle($oTopic->getTitle());
		/**
		 * Задаем шаблон
		 */
		$this->SetTemplateAction('topic');
	}

	public function EventUnread() {
		/**
		 * Если пользователь не залогинен, отдаем ему 404
		 */
		if (!$this->oUserCurrent) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем страницу
		 */
		$iPage=$this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;
		/**
		 *	Получаем дату последней активности пользователя
		 */
		$oUserLast=$this->PluginForum_ModuleForum_GetUserLastActiveById($this->oUserCurrent->getId());
		/**
		 * Получаем топики по этой дате
		 */
		$aTopics=$this->PluginForum_ModuleForum_GetTopicItemsAll(array('#where'=>array('topic_date >= ?' => array($oUserLast->getActive())),'#order'=>array('post_id'=>'desc')));
		/**
		 * В шаблон
		 */
		$this->Viewer_Assign("aTopics",$aTopics);
		/**
		 * Задаем шаблон
		 */
		$this->SetTemplateAction('unread');
	}
	
	/**
	 * Добавление топика
	 */
	public function EventAddTopic() {
		if (!$this->oUserCurrent) {
			return parent::EventNotFound();
		}
		
		$sForumId=$this->GetParamEventMatch(0,1);

		if (!$oForum=$this->PluginForum_ModuleForum_GetForumById($sForumId)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_unknown'),$this->Lang_Get('error'));
			return false;
		}
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isPost('submit_topic_publish')) {
			return $this->SubmitAdd($oForum);
		}

		$this->Viewer_Assign("oForum",$oForum);
	}

	/**
	 * Сабмит формы добавления топика
	 */
	public function SubmitAdd($oForum) {
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkTopicFields()) {
			return false;
		}
		/**
		 * Проверяем права на постинг в форум
		 * Скоро будут права на молчание в определенных форумах
		 *
		if (!$this->ACL_IsAllowBlog($oBlog,$this->oUserCurrent)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_noallow'),$this->Lang_Get('error'));
			return false;
		}
		*/

		/**
		 * Теперь можно смело добавлять топик к форуму
		 */
		$oTopic=LS::Ent('PluginForum_ModuleForum_EntityTopic');
		$oTopic->setForumId($oForum->getId());
		$oTopic->setUserId($this->oUserCurrent->getId());
		$oTopic->setTitle(getRequest('topic_title'));
		$oTopic->setDate(date("Y-m-d H:i:s"));
		$oTopic->setUrl($this->PluginForum_ModuleForum_GenerateUrl(getRequest('topic_title')));
		$oTopic->setViews('0');

		/**
		 *	Статус:
		 *	0 - открыт
		 *	1 - закрыт
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
		 *	1- прикреплен
		 */
		$oTopic->setPosition(0);
		if ($this->oUserCurrent->isAdministrator())	{
			if (getRequest('topic_position')) {
				$oTopic->setPosition(1);
			} 
		}

		$oPost=LS::Ent('PluginForum_ModuleForum_EntityPost');
		$oPost->setUserId($this->oUserCurrent->getId());
		$oPost->setDateAdd(date("Y-m-d H:i:s"));
		$oPost->setForumId($oForum->getId());

		$oPost->setText($this->Text_Parser(getRequest('topic_text')));
		$oPost->setTextSource(getRequest('topic_text'));

		/**
		 * Добавляем топик
		 */
		if ($oTopic->Add()) {
			/**
			 * Получаем топик, чтобы подцепить связанные данные
			 */
			$oTopic=$this->PluginForum_ModuleForum_GetTopicById($oTopic->getId());
			$oPost->setTopicId($oTopic->getId());
			/**
			 * Добавляет первый пост
			 */
			if ($oPost->Add()) {
				/**
				 * Получаем пост, чтоб подцепить связанные данные
				 */
				$oPost=$this->PluginForum_ModuleForum_GetPostById($oPost->getId());
				$oTopic->setLastPostId($oPost->getId());
				$oTopic->Update();

				$oForum->setLastPostId($oPost->getId());
				$oForum->setLastTopicId($oTopic->getId());
				$oForum->setLastUserId($this->oUserCurrent->getId());
				$oForum->Update();

				Router::Location(Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');
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
	public function checkTopicFields() {
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
	 * Добавление поста
	 */
	public function EventAddPost() {
		/**
		 * Проверка формы
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
		if (!($oTopic=$this->PluginForum_ModuleForum_GetTopicById(getRequest('topic_id')))) {
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
		$oPost=LS::Ent('PluginForum_ModuleForum_EntityPost');
		$oPost->setForumId($oForum->getId());
		$oPost->setTopicId($oTopic->getId());
		$oPost->setUserId($this->oUserCurrent->getId());
		$oPost->setText($sText);
		$oPost->setTextSource(getRequest('form_post_text'));
		$oPost->setDateAdd(date("Y-m-d H:i:s"));
		/**
		 * Добавляем
		 */
		if ($oPost->Add()) {

			$oForum->setLastPostId($oPost->getId());
			$oForum->setLastTopicId($oTopic->getId());
			$oForum->setLastUserId($this->oUserCurrent->getId());
			$oForum->Update();

			$oTopic->setLastPostId($oPost->getId());
			$oTopic->Update();

			$this->Viewer_AssignAjax('idPostLast',getRequest('last_post'));
			$this->Viewer_AssignAjax('idForum',$oForum->getId());

			$this->Message_AddNoticeSingle('notice','notice');
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}

	}

	/**
	 * Получение новых постов
	 */
	public function EventResponsePost() {
		/**
		 * Проверка запроса
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверка авторизации
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
		if (!($oTopic=$this->PluginForum_ModuleForum_GetTopicById(getRequest('idTargetTopic')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$idPostLast=getRequest('idPostLast',null,'post');

		$aPosts=array();
		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByTopicId($oTopic->getId(), array('#where'=>array('post_id > ?d' => array($idPostLast))));
		$iMaxIdPost=$aResult[count($aResult)-1]->getId();

		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			if ($oRead=$this->PluginForum_ModuleForum_GetReadByTopicIdAndUserId($oTopic->getId(), $this->oUserCurrent->getId())) {
				$oRead->setTopicId($oTopic->getId());
				$oRead->setUserId($this->oUserCurrent->getId());
				$oRead->setPostId($oTopic->getPostId());
				$oRead->setDate(date("Y-m-d H:i:s"));
				$oRead->Update();
			} else {
				$oRead=LS::Ent('PluginForum_ModuleForum_EntityRead');
				$oRead->setTopicId($oTopic->getId());
				$oRead->setUserId($this->oUserCurrent->getId());
				$oRead->setPostId($oTopic->getPostId());
				$oRead->setDate(date("Y-m-d H:i:s"));
				$oRead->Add();
			}
			$oRead=$this->PluginForum_ModuleForum_GetReadByTopicIdAndUserId($oTopic->getId(), $this->oUserCurrent->getId());
		} else {
			$oRead=null;
		}
		
		$oViewerLocal=$this->Viewer_GetLocalViewer();
		$oViewerLocal->Assign('oUserCurrent',$this->oUserCurrent);
		$oViewerLocal->Assign('oTopic',$oTopic);
		$oViewerLocal->Assign('oForum',$oForum);
		$oViewerLocal->Assign('oRead',$oRead);
		$oViewerLocal->Assign('bAjax',true);

		if ($aResult and is_array($aResult)) {
			foreach ($aResult as $oPost) {
				$oViewerLocal->Assign('oPost',$oPost);
				$sHtml=$oViewerLocal->Fetch(Plugin::GetTemplatePath(__CLASS__).'/post.tpl');
				$aPosts[]=array(
					'id' => $oPost->getId(),
					'html' => $sHtml,
				);
			}
		}

		$this->Viewer_AssignAjax('iMaxIdPost',$iMaxIdPost);
		$this->Viewer_AssignAjax('aPosts',$aPosts);
		$this->Viewer_AssignAjax('oRead',$oRead);
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
	 * Фукнция для получения статистики активности форума
	 */
	public function GetForumStats() {
		/**
		 * Статистика
		 */
		$aTopics=$this->PluginForum_ModuleForum_GetTopicItemsAll(array('#page'=>array(1,1)));
		$aStat['count_all_topics']=$aTopics['count'];
		$aPosts=$this->PluginForum_ModuleForum_GetPostItemsAll(array('#page'=>array(1,1)));
		$aStat['count_all_posts']=$aPosts['count'];
		$sDate=date("Y-m-d H:00:00",time()-60*60*24*1);
		$aToDayPosts=$this->PluginForum_ModuleForum_GetPostItemsAll(array('#where'=>array('post_date_add >= ?' => array($sDate)), '#page'=>array(1,1)));
		$aStat['count_today_posts']=$aPosts['count'];
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForumStat',$aStat);
	}


	private function _adminMain() {
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('admin-main');
	}

	/**
	 * Управление форумами
	 */
	private function _adminForums() {
		/**
		 * Удаление форума
		 */
		if ($this->GetParam(1)=='delete') {
			$iCategoryId=$this->GetRequestCheck('cat_id');
			if ($oCategory=$this->PluginForum_ModuleForum_GetCategoryById($iCategoryId)) {
				$oCategory->Delete();
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}
			Router::Location(Router::GetPath('forum').'admin/forums');
		}

		/**
		 * Обрабатываем создание категории
		 */
		if (isPost('submit_category_add')) {
			$this->Security_ValidateSendForm();

			/**
			 * TODO: Сделать проверку полей
			 */
			$oForum=LS::ENT('PluginForum_Forum');
			$oForum->setTitle(getRequest('category_title',null,'post'));
			$oForum->setType(PluginForum_ModuleForum::FORUM_TYPE_CATEGORY);

			if($this->PluginForum_Forum_AddForum($oForum)) {
				$this->Message_AddNotice($this->Lang_Get('forum_create_category_ok'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}

			return;
		}

		/**
		 * Обрабатываем создание форума
		 */
		if (isPost('submit_forum_add')) {
			$this->Security_ValidateSendForm();

			/**
			 * TODO: Сделать проверку полей
			 */
			$oForum=LS::ENT('PluginForum_Forum');
			$oForum->setTitle(getRequest('forum_title',null,'post'));
			$oForum->setDescription(getRequest('forum_description',null,'post'));
			$oForum->setParentId(getRequest('forum_parent',null,'post'));
			$oForum->setType(PluginForum_ModuleForum::FORUM_TYPE_NORMAL);//пока что так

			if($this->PluginForum_Forum_AddForum($oForum)) {
				$this->Message_AddNotice($this->Lang_Get('forum_create_ok'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}

			return;
		}

		/**
		 * Получаем список форумов
		 */
		$aForums=$this->PluginForum_ModuleForum_LoadTree();

		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForums',$aForums);
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('admin-forums');
	}

	/**
	 *	Админка, пока только заготовка
	 */
	public function EventAdmin() {
		if (!$this->oUserAdmin) {
			return parent::EventNotFound();
		}

		/**
		 * Раздел админки
		 */
		$sDir = $this->GetParam(0);

		switch ($sDir) {
			/**
			 * Управление форумами
			 */
			case 'forums':
				$this->_adminForums();
				break;
			/**
			 * Главная
			 */
			default:
				$this->_adminMain();
				break;
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

		$this->Viewer_Assign('FORUM_TYPE_CATEGORY',PluginForum_ModuleForum::FORUM_TYPE_CATEGORY);
		$this->Viewer_Assign('FORUM_TYPE_LINK',PluginForum_ModuleForum::FORUM_TYPE_LINK);
		$this->Viewer_Assign('FORUM_TYPE_PASSWORD',PluginForum_ModuleForum::FORUM_TYPE_PASSWORD);
	}
}
?>
