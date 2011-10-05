{if $oUserCurrent}
<ul class="switcher">
	{if $sMenuSubItemSelect == 'show_topic'}
		{if $oTopic->getStatus()!=1 || $oUserCurrent->isAdministrator()}
			<li{if $sMenuSubItemSelect=='reply'} class="active"{/if}><a href="{$oTopic->getUrlFull()}reply">{$aLang.forum_reply}</a></li>
		{/if}
	{/if}
	{if $sMenuSubItemSelect == 'show_topic' || $sMenuSubItemSelect == 'show_forum'}
		{if $oForum->getType() != $FORUM_TYPE_CATEGORY}
			<li{if $sMenuSubItemSelect=='add'} class="active"{/if}><a href="{$oForum->getUrlFull()}add">{$aLang.forum_new_topic}</a></li>
		{/if}
	{/if}
</ul>
{/if}
