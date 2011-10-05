{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div id="forum">

	<div class="forum-nav">
		<h2>{include file="$sTemplatePathPlugin/breadcrumbs.tpl"}</h2>
	</div>

	<div class="sv-forum-block">
		<div class="clear_fix">
		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}
		{include file="$sTemplatePathPlugin/switcher_top.tpl"}
		</div>

		<div class="sv-forum_header sv-forum_header-subject_page clear_fix">
			<div class="sv-left_bg">
				<h2>{$oTopic->getTitle()}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-posts_block">
			{foreach from=$aPosts item=oPost}
				{include file="$sTemplatePathPlugin/post.tpl" oPost=$oPost}
			{/foreach}
		</div>

		<div class="sv-shadow sv-shadow-posts_block"></div>

		<div class="clear_fix">
		{include file="$sTemplatePathPlugin/paging.tpl" aPaging=$aPaging}
		{include file="$sTemplatePathPlugin/switcher_top.tpl"}
		</div>
	</div>
</div>

{include file='footer.tpl'}