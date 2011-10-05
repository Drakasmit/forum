<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleUser extends PluginForum_Inherit_ModuleUser {

	/* ПЕРЕПИСАТЬ! */
	/*public function GetUsersAdditionalData($aUserId,$aAllowData) {
		$aUsers=parent::GetUsersAdditionalData($aUserId,&$aAllowData);
		//Добавляем данные к результату
		foreach ($aUsers as $oUser) {
			$oUser->setCountPosts($this->GetCountPosts($oUser->getId()));
			$oUser->setCountTopics($this->GetCountTopics($oUser->getId()));
			$oUser->setCountArticles($this->GetCountArticles($oUser->getId()));
			$oUser->setCountComments($this->GetCountComments($oUser->getId()));
		}
		return $aUsers;
	}*/

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