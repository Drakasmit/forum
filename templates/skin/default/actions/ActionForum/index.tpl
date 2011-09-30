{include file='header.tpl' noSidebar=true}
<div id="forum">

	<div class="forum-nav">
		<h2>{$aLang.forums}</h2>
	</div>

	{if $oUserCurrent}
	<ul class="sv-tom_menu">
		<li><a href="{router page='forum'}unread">{$aLang.not_reading}</a></li>
		<li><a href="#">{$aLang.mark_all_read}</a></li>
		{if $oUserCurrent->isAdministrator()}
		<li><a href="{router page='forum'}admin">{$aLang.forum_acp}</a></li>
		{/if}
	</ul>
	{/if}

	<div class="clear"></div>

{if $aForums}
	{foreach from=$aForums item=oForum}
		{assign var='aSubForums' value=$oForum->getChildren()}
		<div class="sv-forum_header">
			<div class="sv-left_bg">
				<h2><a href="{router page='forum'}{if $oForum->getUrl()}{$oForum->getUrl()}{else}{$oForum->getId()}{/if}">{$oForum->getTitle()}</a></h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-last_msg">{$aLang.last_post}</span>
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.themes}</span>
			</div>
		</div>

		<div class="sv-table_container">
			{if $aSubForums}
			<table class="sv-forum_body">
				<tbody>
				{foreach from=$aSubForums item=oSubForum}
					{assign var="oTopic" value=$oSubForum->getTopic()}
					{assign var="oPost" value=$oSubForum->getPost()}
					{assign var="oUser" value=$oSubForum->getUser()}
					<tr>
						<td class="sv-icon_col">
							<a class="bbl{if $oTopic && $oTopic->getDateRead()<=$oPost->getDate()} new{/if}" href="{router page='forum'}{if $oSubForum->getUrl()}{$oSubForum->getUrl()}{else}{$oSubForum->getId()}{/if}"></a>
						</td>
						<td class="sv-main_col">
							<h3><a href="{router page='forum'}{if $oSubForum->getUrl()}{$oSubForum->getUrl()}{else}{$oSubForum->getId()}{/if}">{$oSubForum->getTitle()}</a></h3>
							<p class="sv-details">{$oSubForum->getDescription()}</p>
						</td>
						<td class="sv-last_msg">
							{if $oTopic}
							<a class="sv-subj" href="{router page='forum'}{if $oSubForum->getUrl()}{$oSubForum->getUrl()}{else}{$oSubForum->getId()}{/if}/{$oTopic->getId()}-{$oTopic->getUrl()}.html">{$oTopic->getTitle()}</a><br />
							<a class="sv-author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
							<span class="sv-date">@ {date_format date=$oPost->getDate()}</span>
							{/if}
						</td>
						<td class="sv-answers">{$oSubForum->getCountPosts()}</td>
						<td class="sv-views">{$oSubForum->getCountTopics()}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{else}
				<div align="center">{$aLang.forums_no}</div>
			{/if}
		</div>
	{/foreach}
{/if}

	<div class="sv-shadow"></div>

	<div class="sv-forum_stats right">
		<span class="sv-small">lsBB by <span class="sv-count"><a href="http://artemeff.ru">artemeff</a></span></span>
		<span class="sv-small">sources on <a href="https://github.com/artemeff/forum">github</a></span>
	</div>

	<div class="sv-forum_stats">
		<h2>Статистика форума</h2>
		<div class="sv-topics">
			<span class="sv-now">Всего топиков/сообщений &mdash; <span class="sv-count">{$aForumStat.count_all_topics}/{$aForumStat.count_all_posts}</span></span>
			<span class="sv-small">Сообщений за сегодня &mdash; {$aForumStat.count_today_posts}</span>
		</div>
	</div>

</div>
{include file='footer.tpl'}