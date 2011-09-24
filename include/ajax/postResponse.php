<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/

/**
 * Получение новых комментов
 */

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(dirname(__FILE__))));
$sDirRoot=dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($sDirRoot."/config/config.ajax.php");

$idTopic=getRequest('idTarget',null,'post');
$bStateError=true;
$sMsg='';
$sMsgTitle='';
$aPosts=array();
if ($oEngine->User_IsAuthorization()) {
	$oUserCurrent=$oEngine->User_GetUserCurrent();
	if ($oTopic=$oEngine->PluginForum_ModuleTopic_GetTopicById($idTopic)) {
		$aReturn=$oEngine->PluginForum_ModulePost_GetNewPostsByTopicId($oTopic->getId()); // PluginForum_ModulePost_GetNewPostsById($oTopic->getId(),$idPostLast)

		$aPsts=$aReturn['collection'];
		
		if ($aPsts and is_array($aPsts)) {
			foreach ($aPsts as $aPst) {
				$aPosts[]=array(
					'html' => $aPst['html'],
					'id' => $aPst['obj']->getId(),
				);
			}
		}
		$bStateError=false;
	} else {
		$sMsgTitle=$oEngine->Lang_Get('error');
		$sMsg=$oEngine->Lang_Get('system_error');
	}
} else {
	$sMsgTitle=$oEngine->Lang_Get('error');
	$sMsg=$oEngine->Lang_Get('need_authorization');
}

$GLOBALS['_RESULT'] = array(
"bStateError"     => $bStateError,
"sMsgTitle"   => $sMsgTitle,
"sMsg"   => $sMsg,
"aPosts" => $aPosts
);

?>