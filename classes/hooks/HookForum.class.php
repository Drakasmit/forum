<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

/**
 * Регистрация хука для вывода ссылки в меню
 *
 */
class PluginForum_HookForum extends Hook {
	public function RegisterHook() {
		$this->AddHook('template_main_menu','Menu');
		$this->AddHook('forum_topic_show','TopicShow');
	}

	public function Menu() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'main_menu.tpl');
	}
	
	public function TopicShow($aParams) {
		$oTopic=$aParams['oTopic'];
        $do_count_visits=(!$this->User_IsAuthorization());
        if (!$do_count_visits) {
			$oUser=$this->User_GetUserCurrent();
			$do_count_visits=$oUser->getId()!=$oTopic->getUserId();
		}
		if ($do_count_visits) {
			$this->PluginForum_ModuleTopic_SetCountViews($oTopic->getCountViews()+1,$oTopic->getId());
		}
	}
	
}
?>