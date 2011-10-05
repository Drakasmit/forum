{if count($aTopics) > 0}
	<table class="sv-forum_body sv-forum_body-section_page">
	{foreach from=$aTopics item=oTopic}
		{assign var="oUser" value=$oTopic->getUser()}
		{assign var="oPost" value=$oTopic->getPost()}
		{assign var="oPoster" value=$oPost->getUser()}
		<tr>
			<td class="sv-icon_col">
				<a class="bbl{if $oTopic->getDateRead()<$oPost->getDateAdd()} new{/if}{if $oTopic->getPosition()==1} info{/if}{if $oTopic->getStatus()==1} close{/if}" href="{router page='forum'}topic/{$oTopic->getId()}"></a>
			</td>
			<td class="sv-main_col">
				<h3 class="clear_fix">
					<a href="{$oTopic->getUrlFull()}">{$oTopic->getTitle()}</a>
					{include file="$sTemplatePathPlugin/paging_post.tpl" aPaging=$oTopic->getPaging()}
				</h3>
				<span class="sv-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
			</td>
			<td class="sv-answers">{$oTopic->getCountPost()}</td>
			<td class="sv-views">{$oTopic->getViews()}</td>
			<td class="sv-last_msg">
				{if $oPoster}
				<span class="sv-date">{date_format date=$oPost->getDateAdd() format='d.m.Y, H:i'}</span>
				<span class="sv-author">{$aLang.by} <a href="{$oPoster->getUserWebPath()}">{$oPoster->getLogin()}</a></span>
				<a class="sv-link_to_msg" href="{$oPost->getUrlFull()}"></a>
				{/if}
			</td>
		</tr>
	{/foreach}
	</table>
{else}
	<div align="center">{$aLang.nothing}</div>
{/if}