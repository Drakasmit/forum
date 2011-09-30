<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}

class PluginForum extends Plugin {
	protected $sTemplatesUrl = "";
	
	protected $aInherits=array(
		'module' => array('ModuleUser'=>'PluginForum_ModuleUser')
	);


	public function Activate() {
		if (!$this->isTableExists('prefix_forum')) {
			$this->ExportSQL(dirname(__FILE__).'/install.sql');
		}
		return true;
	}


	public function Deactivate()	{
		return true;
	}

	public function Init() {
		$sTemplatesUrl = Plugin::GetTemplatePath('PluginForum');
		Config::Set('head.rules.forum', array(
			'path'=>'___path.root.web___/',
			'css' => array(
				'include' => array(
					Plugin::GetTemplateWebPath(__CLASS__)."css/style.css",
					Plugin::GetTemplateWebPath(__CLASS__)."css/inner.css",
				)
			),
			'js' => array(
				'include' => array(
					Plugin::GetTemplateWebPath(__CLASS__)."js/posts.js"
				)
			)
		));
	}

}

?>
