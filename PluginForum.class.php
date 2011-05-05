<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}

class PluginForum extends Plugin {
	protected $sTemplatesUrl = "";

	public function Activate() {
		return true;
	}


	public function Deactivate()	{
		return true;
	}

	public function Init() {
		$sTemplatesUrl = Plugin::GetTemplatePath('PluginForum');
		Config::Set('head.rules.multiupload_zoom', array(
			'path'=>'___path.root.web___/',
			'css' => array(
				'include' => array(
					Plugin::GetTemplateWebPath(__CLASS__)."css/style.css",
					Plugin::GetTemplateWebPath(__CLASS__)."css/inner.css",
				)
			),
		));
	}

}

?>