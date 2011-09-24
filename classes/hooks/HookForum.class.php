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
		$this->AddHook('template_topic_paging','TopicPaging');
	}

	public function Menu() {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'main_menu.tpl');
	}
	
	public function TopicPaging($aParams) {
		$oTopic=$aParams['topic'];
		$oForum=$aParams['forum'];

		$aResult=$this->PluginForum_ModuleForum_GetPostItemsByTopicId($oTopic->getId(), array('#page'=>array(1,Config::Get('plugin.forum.posts.per_page'))));
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