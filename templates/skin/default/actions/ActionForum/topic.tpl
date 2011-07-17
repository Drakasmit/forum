{assign var="bNoSidebar" value=true}
{include file='header.tpl'}

<script type="text/javascript" src="{$sTWP}js/posts.js"></script>

<div id="forum">

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">{$aLang.main_title}</a></span> <span><a href="{router page='forum'}{$oForum->getUrl()}/">{$oForum->getTitle()}</a></span> {$oTopic->getTitle()}
		</h2>
	</div>

	<div class="sv-forum-block">

		{if $oUserCurrent}
		<ul class="sv-tom_menu">
			{if $oTopic->getStatus()!=1 OR $oUserCurrent->isAdministrator()}
			<li class="sv-first"><a class="topbutton" href="#">{$aLang.reply}</a></li>
			{/if}
			<li><a class="topbutton" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
		</ul>
		{/if}


		{if $aPaging and $aPaging.iCountPage>1}
			{include file="$sTP/paging.tpl"}
		{/if}

		<div class="clear"></div>

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>{$oTopic->getTitle()}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-posts_block">
			{foreach from=$aPost item=oPost}
				{include file="$sTP/post.tpl"}
			{/foreach}
			<div class="new_post" id="post_id_new"></div>
		</div>

		<div class="sv-shadow sv-shadow-posts_block"></div>

		{if $oUserCurrent}
		<ul class="sv-bottom_menu">
			{if $oTopic->getStatus()!=1 OR $oUserCurrent->isAdministrator()}
			<li class="sv-first"><a class="topbutton" href="#">{$aLang.reply}</a></li>
			{/if}
			<li><a class="topbutton" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
		</ul>
		{/if}

		{if $aPaging and $aPaging.iCountPage>1}
			{include file="$sTP/paging.tpl"}
		{/if}


		<div class="clear"></div>

		{if $oUserCurrent AND $oTopic->getStatus()!=1 OR $oUserCurrent AND $oUserCurrent->isAdministrator()}
			{include file="$sTP/addpost.tpl"}
		{/if}

	</div>
	
</div>

{include file='footer.tpl'}