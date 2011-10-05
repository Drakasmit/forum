<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

/**
 * Регистрация хуков
 *
 */
class PluginForum_HookForum extends Hook {
	public function RegisterHook() {
		$this->AddHook('template_main_menu','Menu');
		$this->AddHook('template_forum_copyring','Copyring');
	}

	public function Menu() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'main_menu.tpl');
	}

	public function Copyring() {
		$aPlugins=$this->Plugin_GetList();
		if (!(isset($aPlugins['forum']))) {
			return;
		}
		$aForumData=$aPlugins['forum']['property'];
		$this->Viewer_Assign('aForumData',$aForumData);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'copyring.tpl');
	}
}
?>