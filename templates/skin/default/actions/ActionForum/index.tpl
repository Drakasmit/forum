{include file='header.tpl' noSidebar=true}

<div class="forum clear_fix">

	<div class="forum-nav">
		<h2>{$aLang.forums}</h2>
	</div>

{if $aCategories}
	{foreach from=$aCategories item=oCategory}
		{assign var='aForums' value=$oCategory->getChildren()}
		<div class="sv-forum_header clear_fix">
			<div class="sv-left_bg">
				<h2><a href="{$oCategory->getUrlFull()}">{$oCategory->getTitle()}</a></h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-last_msg">{$aLang.last_post}</span>
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.themes}</span>
			</div>
		</div>

		<div class="sv-table_container">
			{include file="$sTemplatePathPlugin/forums_list.tpl"}
		</div>
	{/foreach}
{/if}

	<div class="sv-shadow"></div>

	{if $oUserCurrent}
	<div class="clear_fix">
		<ul class="sv-bottom_menu right">
			<li><a href="{router page='forum'}markread">{$aLang.forum_markread_all}</a></li>
		</ul>
	</div>
	{/if}

	<div class="sv-forum_stats">
		<h2>{$aLang.forum_stat}</h2>
		<div class="sv-topics">
			<span class="sv-now">Всего топиков/сообщений &mdash; <span class="sv-count">{$aForumStat.count_all_topics}/{$aForumStat.count_all_posts}</span></span>
			<span class="sv-small">Сообщений за сегодня &mdash; {$aForumStat.count_today_posts}</span>
		</div>
	</div>

	{hook run='forum_copyring'}
</div>

{include file='footer.tpl'}