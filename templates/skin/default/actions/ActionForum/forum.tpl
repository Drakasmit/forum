{assign var="bNoSidebar" value=true}
{include file='header.tpl'}
<div id="forum">

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">{$aLang.main_title}</a></span> {$oForum->getTitle()}
		</h2>
	</div>

	<div class="sv-forum-block">

		<ul class="sv-tom_menu">
			<li><a class="newtopic" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			<li><a class="notfresh" href="#">{$aLang.mark_all_read}</a></li>
		</ul>

		{if $aPaging and $aPaging.iCountPage>1}
		<div class="sv-forum_nav">
			<div class="sv-numbers">
					{if $aPaging.iCurrentPage>1}
						<a href="{$aPaging.sBaseUrl}">&larr;</a>
					{/if}
					{foreach from=$aPaging.aPagesLeft item=iPage}
						<a href="{$aPaging.sBaseUrl}/page{$iPage}">{$iPage}</a>
					{/foreach}
					{$aPaging.iCurrentPage}
					{foreach from=$aPaging.aPagesRight item=iPage}
						<a href="{$aPaging.sBaseUrl}/page{$iPage}">{$iPage}</a>
					{/foreach}
					{if $aPaging.iCurrentPage<$aPaging.iCountPage}
						<a href="{$aPaging.sBaseUrl}/page{$aPaging.iCountPage}">{$aLang.paging_last}</a>
					{/if}					
			</div>
		</div>
		{/if}

		<div class="clear"></div>

		<div class="sv-forum_header sv-forum_header-section_page">
			<div class="sv-left_bg"><h2>{$oForum->getTitle()}</h2></div>
			<div class="sv-right_bg">
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.views}</span>
				<span class="sv-last_msg">{$aLang.last_post}</span>
			</div>
		</div>
		

		<div class="sv-table_container">
			<table class="sv-forum_body sv-forum_body-section_page">
				<tbody>
					{foreach from=$aTopics item=oTopic}
						{assign var="oUser" value=$oTopic->getUser()}
						{assign var="oPost" value=$oTopic->getPost()}
						{assign var="oUserLast" value=$oTopic->getUserLast()}
						<tr>
							<td class="sv-icon_col">
								<a class="bbl" href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html"></a>
							</td>
							<td class="sv-main_col">
								<h3>
									<a href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html">{$oTopic->getTitle()}</a>
									<span class="sv-go_to_page">[ {$aLang.on_page}: 1, 2 ]</span>
								</h3>
								<span class="sv-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
							</td>
							<td class="sv-answers">{$oTopic->getCountPosts()}</td>
							<td class="sv-views">{$oTopic->getCountViews()}</td>
							<td class="sv-last_msg">
								{if $oPost}
								<span class="sv-date">{date_format date=$oPost->getDate()}</span>
								<span class="sv-author">{$aLang.by} <a href="{$oUserLast->getUserWebPath()}">{$oUserLast->getLogin()}</a></span>
								{/if}
							</td>
						</tr>
					{foreachelse}
						{$aLang.nothing}
					{/foreach}
				</tbody>
			</table>
		</div>

		<div class="sv-shadow"></div>

		{if $aPaging and $aPaging.iCountPage>1}
		<div class="sv-forum_nav">
			<div class="sv-numbers">
					{if $aPaging.iCurrentPage>1}
						<a href="{$aPaging.sBaseUrl}">&larr;</a>
					{/if}
					{foreach from=$aPaging.aPagesLeft item=iPage}
						<a href="{$aPaging.sBaseUrl}/page{$iPage}">{$iPage}</a>
					{/foreach}
					{$aPaging.iCurrentPage}
					{foreach from=$aPaging.aPagesRight item=iPage}
						<a href="{$aPaging.sBaseUrl}/page{$iPage}">{$iPage}</a>
					{/foreach}
					{if $aPaging.iCurrentPage<$aPaging.iCountPage}
						<a href="{$aPaging.sBaseUrl}/page{$aPaging.iCountPage}">{$aLang.paging_last}</a>
					{/if}					
			</div>
		</div>
		{/if}

		<ul class="sv-bottom_menu">
			<li><a class="newtopic" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			<li><a class="notfresh" href="#">{$aLang.mark_all_read}</a></li>
		</ul>

	</div>

</div>
{include file='footer.tpl'}