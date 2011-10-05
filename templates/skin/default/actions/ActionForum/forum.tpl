{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div class="forum clear_fix">
	{assign var='aSubForums' value=$oForum->getChildren()}

	<div class="forum-nav">
		<h2>{include file="$sTemplatePathPlugin/breadcumbs.tpl"}</h2>
	</div>

	<div class="sv-forum-block">
		{if $aSubForums}
		<!-- Список форумов -->
		<div class="sv-forum_header clear_fix">
			<div class="sv-left_bg">
				<h2><a href="{$oForum->getUrlFull()}">{$oForum->getTitle()} - {$aLang.forum_subforums}</a></h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-last_msg">{$aLang.last_post}</span>
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.themes}</span>
			</div>
		</div>

		<div class="sv-table_container clear_fix">
			{include file="$sTemplatePathPlugin/forums_list.tpl" aForums=$aSubForums}
		</div>
		<!--/ Список форумов -->
		{/if}

		<div class="clear_fix">
		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}
		{include file="$sTemplatePathPlugin/switcher_top.tpl"}
		</div>

		{if $oForum->getType() != $FORUM_TYPE_CATEGORY}
		<!-- Список тем -->
		<div class="sv-forum_header sv-forum_header-section_page clear_fix">
			<div class="sv-left_bg">
				<h2>{$oForum->getTitle()}</h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.views}</span>
				<span class="sv-last_msg">{$aLang.last_post}</span>
			</div>
		</div>

		<div class="sv-table_container clear_fix">
		{include file="$sTemplatePathPlugin/topics_list.tpl"}
		</div>
		<!--/ Список тем -->
		{/if}

		<div class="sv-shadow"></div>

		<div class="clear_fix">
		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}
		{include file="$sTemplatePathPlugin/switcher_top.tpl"}
		</div>
	</div>

</div>
{include file='footer.tpl'}