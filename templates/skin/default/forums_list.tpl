{if count($aForums) > 0}
	<table class="sv-forum_body">
		{foreach from=$aForums item=oForum}
			{assign var="oTopic" value=$oForum->getTopic()}
			{assign var="oPost" value=$oForum->getPost()}
			{assign var="oUser" value=$oForum->getUser()}
			{assign var='aSubForums' value=$oForum->getChildren()}
			<tr>
				<td class="sv-icon_col">
					<a class="bbl{if $oTopic && $oTopic->getDateRead()<=$oPost->getDateAdd()} new{/if}" href="{$oForum->getUrlFull()}"></a>
				</td>
				<td class="sv-main_col">
					<h3><a href="{$oForum->getUrlFull()}">{$oForum->getTitle()}</a></h3>
					<p class="sv-details">{$oForum->getDescription()}</p>
					{if $aSubForums}
					<p class="sv-details">
						<strong>{$aLang.forum_subforums}:</strong>
						{foreach from=$aSubForums item=oSubForum name=subforums}
						<a href="{$oSubForum->getUrlFull()}">{$oSubForum->getTitle()}</a>{if !$smarty.foreach.subforums.last}, {/if}
						{/foreach}
					</p>
					{/if}
				</td>
				<td class="sv-last_msg">
					{if $oTopic && $oPost}
					<a class="sv-subj" href="{$oTopic->getUrlFull()}">{$oTopic->getTitle()}</a>
					<a class="sv-link_to_msg" href="{$oPost->getUrlFull()}"></a><br />
					<a class="sv-author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
					<span class="sv-date">@ {date_format date=$oPost->getDateAdd()}</span>
					{/if}
				</td>
				<td class="sv-answers">{$oForum->getCountPost()}</td>
				<td class="sv-views">{$oForum->getCountTopic()}</td>
			</tr>
		{/foreach}
	</table>
{else}
	<div align="center">{$aLang.forums_no}</div>
{/if}