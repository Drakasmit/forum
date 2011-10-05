{assign var="noSidebar" value=true}
{include file='header.tpl'}
<div id="forum">

	<div class="forum-nav">
		<h2>{include file="$sTemplatePathPlugin/breadcumbs.tpl"}</h2>
	</div>

	<div class="sv-forum-block">
		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}

		<div class="sv-forum_header sv-forum_header-section_page clear_fix">
			<div class="sv-left_bg"><h2>{$aLang.forum_not_reading}</h2></div>
			<div class="sv-right_bg">
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.views}</span>
				<span class="sv-last_msg">{$aLang.last_post}</span>
			</div>
		</div>

		<div class="sv-table_container clear_fix">
		{include file="$sTemplatePathPlugin/topics_list.tpl"}
		</div>

		<div class="sv-shadow"></div>

		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}
	</div>

</div>
{include file='footer.tpl'}