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
		$this->AddHook('forum_topic_show','TopicShow');
		$this->AddHook('template_main_menu','Menu');
		$this->AddHook('template_topic_paging','TopicPaging');
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
	
	public function TopicPaging($aParams) {
		$oTopic=$aParams['topic'];
		$oForum=$aParams['forum'];

		$aResult=$this->PluginForum_ModulePost_GetPostsByTopicId($oTopic->getId(),1,Config::Get('plugin.forum.posts.per_page'));
		$aPaging=$this->Viewer_MakePaging($aResult['count'],1,Config::Get('plugin.forum.posts.per_page'),4,Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html');

		if ($aResult['count']>Config::Get('plugin.forum.posts.per_page')) {
			$oPages='[ '.$this->Lang_Get('on_page').': ';
			$oPages.='<a href="'.Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html">1</a>, ';
			foreach ($aPaging['aPagesRight'] as $iPage) {
				$oPages.='<a href="'.Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html/page'.$iPage.'">'.$iPage.'</a>';
				if ($iPage < $aPaging['iCountPage'] AND $iPage < 5) { $oPages.=', '; } else { $oPages.=' ';}
			}
			if ($aPaging['iCountPage']>5) {
				$oPages.='... ';
				$oPages.='<a href="'.Router::GetPath('forum').$oForum->getUrl().'/'.$oTopic->getId().'-'.$oTopic->getUrl().'.html/page'.$aPaging['iCountPage'].'">'.$aPaging['iCountPage'].'</a>';
			}
			$oPages.=' ]';
			return $oPages;
		}
	}
	
}
?>