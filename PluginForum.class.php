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
	protected $aInherits=array(
		'module' => array('ModuleUser'=>'PluginForum_ModuleUser')
	);

	/**
	 * ��������� �������
	 */
	public function Activate() {
		if (!$this->isTableExists('prefix_forum_list')) {
			$this->ExportSQL(dirname(__FILE__).'/sql/install.sql');
		}
		return true;
	}

	/**
	 * ����������� �������
	 */
	public function Deactivate() {
		if (Config::Get('plugin.forum.deactivate.delete')) {
			$this->ExportSQL(dirname(__FILE__).'/sql/deinstall.sql');
		}
		return true;
	}

	/**
	 * ������������� �������
	 */
	public function Init() {
		return true;
	}

}

?>