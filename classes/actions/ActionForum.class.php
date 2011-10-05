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
	 * Главное меню
	 */
	protected $sMenuHeadItemSelect='forum';
	/**
	 * Меню
	 */
	protected $sMenuItemSelect='forum';
	/**
	 * Подменю
	 */
	protected $sMenuSubItemSelect='';
	/**
	 * Хлебные крошки
	 */
	protected $aBreadcrumbs=array();
	/**
	 * Заголовки
	 */
	protected $aTitles=array('before'=>array(),'after'=>array());
	/**
	 * Список запрещенных URL
	 */
	protected $aBadUrl=array('new','create','admin');

	/**
	 * Инициализация экшена
	 */
	public function Init() {
		/**
		 * Получаем текущего пользователя
		 */
		$this->oUserCurrent=$this->User_GetUserCurrent();
		if ($this->oUserCurrent && $this->oUserCurrent->isAdministrator()) {
			$this->oUserAdmin=true;
		}
		/**
		 * Меню
		 */
		$this->Viewer_AddMenu('forum',$this->getTemplatePathPlugin().'menu.forum.tpl');
		/**
		 * Подключаем CSS
		 */
		$this->Viewer_AppendStyle($this->getTemplatePathPlugin().'css/style.css');
		$this->Viewer_AppendStyle($this->getTemplatePathPlugin().'css/inner.css');
		/**
		 * Подключаем JS
		 */
		$this->Viewer_AppendScript($this->getTemplatePathPlugin().'js/forum.js');
		/**
		 * Заголовок
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('forums'));
		/**
		 * Устанавливаем дефолтный эвент
		 */
		$this->SetDefaultEvent('index');
		/**
		 * Устанавливаем дефолтный шаблон
		 */
		$this->SetTemplateAction('index');
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
		 * Пользовательская часть
		 */
		$this->AddEvent('index','EventIndex');
		$this->AddEvent('markread','EventMarkread');
		$this->AddEventPreg('/^unread$/i','/^(page(\d+))?$/i','EventUnread');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(page(\d+))?$/i','EventShowForum');
		$this->AddEventPreg('/^(\d+)$/i','/^(page(\d+))?$/i','EventShowForum');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^add$/i','EventAddTopic');
		$this->AddEventPreg('/^(\d+)$/i','/^add$/i','EventAddTopic');
		$this->AddEventPreg('/^topic$/i','/^(\d+)$/i','/^(page(\d+))?$/i','EventShowTopic');
		$this->AddEventPreg('/^topic$/i','/^(\d+)$/i','/^reply$/i','EventAddPost');
		/**
		 * AJAX Обработчики
		 */
		$this->AddEventPreg('/^ajax$/i','/^deleteforum$/','EventAjaxDeleteForum');
	}


	/**
	 * Отмечаем все что можно прочтенным
	 */
	public function EventMarkread() {
		Router::Location(Router::GetPath('forum'));
	}

	/**
	 * Главная страница форума
	 *
	 */
	public function EventIndex() {
		/**
		 * Получаем список форумов
		 */
		$aCategories=$this->PluginForum_ModuleForum_LoadTree();
		/**
		 * Получаем статистику
		 */
		$this->GetForumStats();
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aCategories',$aCategories);
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');
	}


	/**
	 * Просмотр форума
	 */
	public function EventShowForum() {
		$this->sMenuSubItemSelect='show_forum';
		/**
		 * Получаем URL форума из эвента
		 */
		$sUrl=$this->sCurrentEvent;
		/**
		 * Получаем форум по URL
		 */
		if(!($oForum=$this->PluginForum_ModuleForum_GetForumByUrl($sUrl))) {
			/**
			 * Возможно форум запросили по id
			 */
			if(!($oForum=$this->PluginForum_ModuleForum_GetForumById($sUrl))) {
				return parent::EventNotFound();
			}
			if($oForum->getUrl()){
				Router::Location($oForum->getUrlFull());
			}
		}
		/**
		 * Получаем текущую страницу
		 */
		$iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;
		/**
		 * Получаем топики
		 */
		$aResult=$this->PluginForum_ModuleForum_GetTopicItemsByForumId($oForum->getId(),array('#order'=>array('topic_position'=>'desc', 'last_post_id'=>'desc', 'topic_date'=>'desc'),'#page' => array($iPage,Config::Get('plugin.forum.topic_per_page'))));
		$aTopics=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.topic_per_page'),4,$oForum->getUrlFull());
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("aPaging",$aPaging);
		$this->Viewer_Assign("aTopics",$aTopics);
		$this->Viewer_Assign("oForum",$oForum);
		/**
		 * Хлебные крошки
		 */
		$this->_breadcrumbsCreate($oForum);
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('forum');
	}

	/**
	 * Просмотр топика
	 */
	public function EventShowTopic() {
		$this->sMenuSubItemSelect='show_topic';
		/**
		 * Получаем ID топика из URL
		 */
		$sId=$this->GetParamEventMatch(0,1);
		/**
		 * Получаем топик по ID
		 */
		if(!($oTopic=$this->PluginForum_ModuleForum_GetTopicById($sId))) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем форум
		 */
		if(!($oForum=$oTopic->getForum())) {
			return parent::EventNotFound();
		}
		/**
		 * Счетчик просмотров топика
		 */
		$oTopic->setViews((int)$oTopic->getViews()+1);
		$oTopic->Save();
		/**
		 * Получаем номер страницы
		 */
		$iPage=$this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;
		/**
		 * Получаем посты
		 */
		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByTopicId($oTopic->getId(),array('#page'=>array($iPage,Config::Get('plugin.forum.post_per_page'))));
		$aPosts=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('plugin.forum.post_per_page'),4,$oTopic->getUrlFull());
		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			if (!$oRead=$this->PluginForum_ModuleForum_GetReadByTopicIdAndUserId($oTopic->getId(),$this->oUserCurrent->getId())) {
				$oRead=LS::Ent('PluginForum_ModuleForum_EntityRead');
				$oRead->setTopicId($oTopic->getId());
				$oRead->setUserId($this->oUserCurrent->getId());
			}
			$oRead->setPostId($oTopic->getLastPostId());
			$oRead->setDate(date("Y-m-d H:i:s"));
			$oRead->Save();
		} else {
			$oRead=null;
		}
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_Assign("oTopic",$oTopic);
		$this->Viewer_Assign("aPosts",$aPosts);
		$this->Viewer_Assign("oRead",$oRead);
		$this->Viewer_Assign("aPaging",$aPaging);
		/**
		 * Хлебные крошки
		 */
		$this->_breadcrumbsCreate($oTopic,true);
		$this->_breadcrumbsCreate($oForum,false);
		/**
		 * Задаем шаблон
		 */
		$this->SetTemplateAction('topic');
	}

	/**
	 * Непрочитанные топики
	 */
	public function EventUnread() {
		/**
		 * Проверяем авторизован ли пользователь
		 */
		if (!$this->oUserCurrent) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем страницу
		 */
		$iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;
		/**
		 * Получаем дату последней активности пользователя
		 */
		$oUserSession=$this->oUserCurrent->getSession();
		/**
		 * Получаем топики по этой дате
		 */
		$aTopics=$this->PluginForum_ModuleForum_GetTopicItemsAll(array('#where'=>array('topic_date >= ?'=>array($oUserSession->getDateLast())),'#order'=>array('last_post_id'=>'desc')));
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("aTopics",$aTopics);
		/**
		 * Хлебные крошки
		 */
		$this->_breadcrumbsAdd($this->Lang_Get('forum_not_reading'),Router::GetPath('forum').'unread/');
		/**
		 * Заголовки
		 */
		$this->_addTitle($this->Lang_Get('forum_not_reading'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('unread');
	}


	/**
	 * Добавление топика
	 */
	public function EventAddTopic() {
		$this->sMenuSubItemSelect='add';
		/**
		 * Проверяем авторизован ли пользователь
		 */
		if (!$this->oUserCurrent) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем URL форума из эвента
		 */
		$sForumUrl=$this->sCurrentEvent;
		/**
		 * Получаем форум по URL
		 */
		if(!($oForum=$this->PluginForum_ModuleForum_GetForumByUrl($sForumUrl))) {
			/**
			 * Возможно форум запросили по id
			 */
			if(!($oForum=$this->PluginForum_ModuleForum_GetForumById($sForumUrl))) {
				return parent::EventNotFound();
			}
		}
		/**
		 * Загружаем перемененные в шаблон
		 */
		$this->Viewer_Assign("oForum",$oForum);
		/**
		 * Хлебные крошки
		 */
		$this->_breadcrumbsCreate($oForum);
		/**
		 * Заголовки
		 */
		$this->_addTitle($this->Lang_Get('forum_new_topic_for')." {$oForum->getTitle()}",'after');
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('add_topic');
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isPost('submit_topic_publish')) {
			return $this->SubmitTopicAdd($oForum);
		}
	}

	/**
	 * Обрабатываем форму добавления топика
	 */
	public function SubmitTopicAdd($oForum) {
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
		 *	1 - прикреплен
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
				$oTopic->setCountPost((int)$oTopic->getCountPost()+1);
				$oTopic->Save();

				$oForum->setLastPostId($oPost->getId());
				$oForum->setLastTopicId($oTopic->getId());
				$oForum->setLastUserId($this->oUserCurrent->getId());
				$oForum->setCountTopic((int)$oForum->getCountTopic()+1);
				$oForum->setCountPost((int)$oForum->getCountPost()+1);
				$oForum->Save();

				Router::Location($oTopic->getUrlFull());
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
	 * Добавление топика
	 */
	public function EventAddPost() {
		$this->sMenuSubItemSelect='reply';
		/**
		 * Проверяем авторизован ли пользователь
		 */
		if (!$this->oUserCurrent) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем ID топика из URL
		 */
		$sTopicId=$this->GetParam(0);
		/**
		 * Получаем топик по ID
		 */
		if(!($oTopic=$this->PluginForum_ModuleForum_GetTopicById($sTopicId))) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем форум
		 */
		if(!($oForum=$oTopic->getForum())) {
			return parent::EventNotFound();
		}
		/**
		 * Загружаем перемененные в шаблон
		 */
		$this->Viewer_Assign("oForum",$oForum);
		$this->Viewer_Assign("oTopic",$oTopic);
		/**
		 * Хлебные крошки
		 */
		$this->_breadcrumbsCreate($oForum);
		/**
		 * Заголовки
		 */
		$this->_addTitle($this->Lang_Get('forum_reply_for',array('topic'=>$oTopic->getTitle())),'after');
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('add_post');
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isPost('submit_post_publish')) {
			return $this->SubmitPostAdd($oForum,$oTopic);
		}
	}

	/**
	 * Добавление поста
	 */
	public function SubmitPostAdd($oForum=null,$oTopic=null) {
		if (!($oForum && $oTopic)) {
			return false;
		}
		/**
		 * Проверяем заголовок поста
		 */
		if ($sPostTitle=getRequest('post_title',null,'post')) {
			if (!func_check($sPostTitle,'text',2,100)) {
				$this->Message_AddError($this->Lang_Get('forum_post_create_title_error'),$this->Lang_Get('error'));
				return false;
			}
		}
		/**
		 * Проверяем есть ли содержание поста
		 */
		$sPostText=$this->Text_Parser(getRequest('post_text','','post'));
		if (!func_check($sPostText,'text',2,Config::Get('plugin.forum.post_max_length'))) {
			$this->Message_AddError($this->Lang_Get('forum_post_create_text_error',array('count'=>Config::Get('plugin.forum.post_max_length'))),$this->Lang_Get('error'));
			return false;
		}
		/**
		 * Проверяем не закрыт ли топик
		 */
		if ($oTopic->getStatus()==1 AND !$this->oUserAdmin) {
			$this->Message_AddError($this->Lang_Get('forum_reply_notallow'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Создаём
		 */
		$oPost=LS::Ent('PluginForum_ModuleForum_EntityPost');
		$oPost->setForumId($oForum->getId());//зачем?
		$oPost->setTopicId($oTopic->getId());
		$oPost->setUserId($this->oUserCurrent->getId());
		$oPost->setText($sPostText);
		$oPost->setTextSource(getRequest('post_text'));
		$oPost->setDateAdd(date("Y-m-d H:i:s"));
		/**
		 * Добавляем
		 */
		if ($oPost->Add()) {
			/**
			 * Обновляем инфу в форуме
			 */
			$oForum->setLastPostId($oPost->getId());
			$oForum->setLastTopicId($oTopic->getId());
			$oForum->setLastUserId($this->oUserCurrent->getId());
			$oForum->setCountPost((int)$oForum->getCountPost()+1);
			$oForum->Save();
			/**
			 * Обновляем инфу в топике
			 */
			$oTopic->setUserId($this->oUserCurrent->getId());
			$oTopic->setLastPostId($oPost->getId());
			$oTopic->setCountPost((int)$oTopic->getCountPost()+1);
			$oTopic->Save();
			Router::Location($oPost->getUrlFull());
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
	}

	/**
	 * Статистика
	 */
	public function GetForumStats() {
		$aStat=array();
		/**
		 * Получаем количество всех топиков
		 */
		$aTopics=$this->PluginForum_ModuleForum_GetTopicItemsAll(array('#page'=>array(1,1)));
		$aStat['count_all_topics']=$aTopics['count'];
		/**
		 * Получаем количество всех постов
		 */
		$aPosts=$this->PluginForum_ModuleForum_GetPostItemsAll(array('#page'=>array(1,1)));
		$aStat['count_all_posts']=$aPosts['count'];
		/**
		 * Получаем количество постов за текущий день
		 */
		$sDate=date("Y-m-d H:00:00",time()-60*60*24*1);
		$aToDayPosts=$this->PluginForum_ModuleForum_GetPostItemsAll(array('#where'=>array('post_date_add >= ?' => array($sDate)), '#page'=>array(1,1)));
		$aStat['count_today_posts']=$aPosts['count'];
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForumStat',$aStat);
	}


	private function _adminMain() {
		$this->sMenuSubItemSelect='main';
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('admin-main');
	}

	/**
	 * Управление форумами
	 */
	private function _adminForums() {
		$this->sMenuSubItemSelect='forums';
		/**
		 * Получаем список форумов
		 */
		$aForums=$this->PluginForum_ModuleForum_LoadTree();
		/**
		 * Дерево форумов
		 */
		$aForumsList=create_forum_list($aForums);
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aForums',$aForums);
		$this->Viewer_Assign('aForumsList',$aForumsList);
		/**
		 * Загружаем в шаблон JS текстовки
		 */
		 $this->Lang_AddLangJs(array('forum_delete_confirm'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('admin-forums');
		/**
		 * Обрабатываем создание категории
		 */
		if (isPost('submit_category_add')) {
			if (!$this->checkForumFields()) {
				return;
			}

			$oForum=LS::ENT('PluginForum_Forum');
			$oForum->setTitle(getRequest('category_title',null,'post'));
			$oForum->setUrl(getRequest('category_url','','post'));
			$oForum->setType(PluginForum_ModuleForum::FORUM_TYPE_CATEGORY);

			if($this->PluginForum_Forum_AddForum($oForum)) {
				$this->Message_AddNotice($this->Lang_Get('forum_create_category_ok'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}

			Router::Location(Router::GetPath('forum').'admin/forums/');
			return;
		}

		/**
		 * Обрабатываем создание форума
		 */
		if (isPost('submit_forum_add')) {
			if (!$this->checkForumFields()) {
				return;
			}

			$oForum=LS::ENT('PluginForum_Forum');
			$oForum->setTitle(getRequest('forum_title',null,'post'));
			$oForum->setUrl(getRequest('forum_url',null,'post'));
			$oForum->setDescription(getRequest('forum_description',null,'post'));
			$oForum->setParentId(getRequest('forum_parent',null,'post'));
			$oForum->setType(PluginForum_ModuleForum::FORUM_TYPE_NORMAL);

			if($this->PluginForum_Forum_AddForum($oForum)) {
				$this->Message_AddNotice($this->Lang_Get('forum_create_ok'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}

			Router::Location(Router::GetPath('forum').'admin/forums/');
			return;
		}
	}

	/**
	 * Админка
	 */
	public function EventAdmin() {
		if (!$this->oUserAdmin) {
			return parent::EventNotFound();
		}

		$this->sMenuItemSelect='admin';

		/**
		 * Подключаем JS
		 */
		$this->Viewer_AppendScript($this->getTemplatePathPlugin().'js/admin.js');

		/**
		 * Раздел админки
		 */
		switch ($this->GetParam(0)) {
			/**
			 * Управление форумами
			 */
			case 'forums':
				$this->_adminForums();
				break;
			/**
			 * Главная
			 */
			case '':
			case null:
				$this->_adminMain();
				break;
			default:
				return parent::EventNotFound();
		}
	}


	/**
	 * Проверка полей формы создания топика
	 *
	 */
	private function checkTopicFields() {
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
	 * Проверка полей формы создания форума
	 *
	 */
	private function checkForumFields() {
		$this->Security_ValidateSendForm();

		$bOk=true;
		/**
		 * Проверяем есть ли заголовок
		 */
		if (!func_check(getRequest('forum_title',null,'post'),'text',2,200)) {
			$this->Message_AddError($this->Lang_Get('forum_create_title_error'),$this->Lang_Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем URL
		 */
		if ($sForumUrl=getRequest('forum_url',null,'post')) {
			$sForumUrl=strtolower(trim($sForumUrl));
			$sForumUrl=preg_replace("/\s+/",'_',$sForumUrl);
			if (!func_check($sForumUrl,'login',3,50)) {
				$this->Message_AddError($this->Lang_Get('forum_create_url_error'),$this->Lang_Get('error'));
				$bOk=false;
			}
			/**
			 * Проверяем на счет плохих URL'ов
			 */
			if(in_array($sForumUrl,$this->aBadUrl)) {
				$this->Message_AddError($this->Lang_Get('forum_create_url_error_badword').' '.implode(',',$this->aBadClubUrl),$this->Lang_Get('error'));
				$bOk=false;
			}
			/**
			 * А не занят ли URL
			 */
			if ($this->PluginForum_ModuleForum_GetForumByUrl($sForumUrl)) {
				$this->Message_AddError($this->Lang_Get('forum_create_url_error_used'),$this->Lang_Get('error'));
				$bOk=false;
			}
			$_REQUEST['forum_url']=$sForumUrl;
		}

		/**
		 * Выполнение хуков
		 */
		$this->Hook_Run('check_forum_fields', array('bOk'=>&$bOk));

		return $bOk;
	}

	/**
	 * Хлебные крошки для объектов (форум\топик\пост)
	 */
	private function _breadcrumbsCreate($oItem,$bClear=true) {
		if (!($oItem instanceof EntityORM)) return;

		if ($bClear) $this->aBreadcrumbs=array();

		$this->aBreadcrumbs[]=array('title'=>$oItem->getTitle(),'url'=>$oItem->getUrlFull(),'obj'=>$oItem);

		if ($oItem->getParentId() && $oParent=$oItem->getParent()) {
			$this->_breadcrumbsCreate($oParent,false);
		}
	}
	/**
	 * Хлебные крошки для всего остального
	 */
	private function _breadcrumbsAdd($sTitle,$sUrl,$bClear=false) {
		if ($bClear) $this->aBreadcrumbs=array();

		$this->aBreadcrumbs[]=array('title'=>$sTitle,'url'=>$sUrl);
	}

	/**
	 * Заголовки
	 */
	 private function _addTitle($sTitle=null,$sAction='before') {
		if (!(in_array($sAction,array('before','after')))) {
			$sAction='before';
		}
		if ($sTitle)
		$this->aTitles[$sAction][]=$sTitle;
	}

	/**
	 * AJAX: Обработка удаления форума
	 */
	protected function EventAjaxDeleteForum() {
		$this->Viewer_SetResponseAjax('json');

		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		if (!$this->oUserAdmin) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return;
		}
		if (!($oForum=$this->PluginForum_ModuleForum_GetForumById(getRequest('idForum',null,'post')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		$aChildrens=$oForum->getChildren();
		if ($oForum->Delete()) {
			//рекурс на удаление всей ветки
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		$this->Message_AddNoticeSingle($this->Lang_Get('forum_delete_ok'),$this->Lang_Get('attention'));
	}


	/**
	 * Завершение работы экшена
	 */
	public function EventShutdown() {
		/**
		 * Titles. Before breadcrumbs
		 */
		foreach ($this->aTitles['before'] as $sTitle) {
			$this->Viewer_AddHtmlTitle($sTitle);
		}
		/**
		 * Breadcrumbs
		 */
		if (!empty($this->aBreadcrumbs)) {
			$this->aBreadcrumbs=array_reverse($this->aBreadcrumbs);
			foreach ($this->aBreadcrumbs as $aItem) {
				$this->Viewer_AddHtmlTitle($aItem['title']);
			}
		}
		/**
		 * Titles. After breadcrumbs
		 */
		foreach ($this->aTitles['after'] as $sTitle) {
			$this->Viewer_AddHtmlTitle($sTitle);
		}
		/**
		 * Загружаем в шаблон необходимые переменные
		 */
        $this->Viewer_Assign('menu','forum');
		$this->Viewer_Assign('aBreadcrumbs',$this->aBreadcrumbs);
		$this->Viewer_Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
		$this->Viewer_Assign('sMenuItemSelect',$this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);
		$this->Viewer_Assign('FORUM_TYPE_NORMAL',PluginForum_ModuleForum::FORUM_TYPE_NORMAL);
		$this->Viewer_Assign('FORUM_TYPE_CATEGORY',PluginForum_ModuleForum::FORUM_TYPE_CATEGORY);
	}
}
?>