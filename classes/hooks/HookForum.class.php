<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

/**
 * Регистрация хука для вывода ссылки в меню
 *
 */
class PluginForum_HookForum extends Hook {
	public function RegisterHook() {
		$this->AddHook('template_main_menu','Menu');
	}

	public function Menu() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'main_menu.tpl');
	}
}
?>