<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

class PluginForum_ModuleUser extends PluginForum_Inherit_ModuleUser {

	public function GetUsersAdditionalData($aUserId,$aAllowData=array('vote','session','friend')) {
		$aUsers=parent::GetUsersAdditionalData($aUserId,$aAllowData=array('vote','session','friend'));
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aUsers as $oUser) {
			$oUser->setCountPosts($this->PluginForum_ModuleUser_GetCountPosts($oUser->getId()));
			$oUser->setCountTopics($this->PluginForum_ModuleUser_GetCountTopics($oUser->getId()));
			$oUser->setCountArticles($this->PluginForum_ModuleUser_GetCountArticles($oUser->getId()));
			$oUser->setCountComments($this->PluginForum_ModuleUser_GetCountComments($oUser->getId()));
		}
		return $aUsers;
	}
	
	public function GetCountPosts($iUserId) {
		return Engine::GetMapper('PluginForum_ModuleUser')->GetCountPosts($iUserId);
	}

	public function GetCountTopics($iUserId) {
		return Engine::GetMapper('PluginForum_ModuleUser')->GetCountTopics($iUserId);
	}
	
	public function GetCountArticles($iUserId) {
		return Engine::GetMapper('PluginForum_ModuleUser')->GetCountArticles($iUserId);
	}
	
	public function GetCountComments($iUserId) {
		return Engine::GetMapper('PluginForum_ModuleUser')->GetCountComments($iUserId);
	}

}

?>