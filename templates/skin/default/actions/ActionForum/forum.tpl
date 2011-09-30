{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div id="forum">
	{assign var='aSubForums' value=$oForum->getChildren()}

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">{$aLang.forums}</a></span> {$oForum->getTitle()}
		</h2>
	</div>

	<div class="sv-forum-block">
		{if $oUserCurrent}
		<ul class="sv-tom_menu">
			{if $oForum->getType() != $FORUM_TYPE_CATEGORY}
			<!-- тоже пока что так -->
			<li><a class="topbutton" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			{/if}
			<li><a class="topbutton" href="#">{$aLang.mark_all_read}</a></li>
		</ul>
		<div class="clear"></div>
		{/if}

		{if $aSubForums}
		<!-- Список форумов -->
		<div class="sv-forum_header">
			<div class="sv-left_bg">
				<h2>{$aLang.forum_subforums}</h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-last_msg">{$aLang.last_post}</span>
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.themes}</span>
			</div>
		</div>

		<div class="sv-table_container">
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
		</div>
		<!--/ Список форумов -->
		<div class="clear"></div>
		{/if}

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

		<div class="clear"></div>
		{/if}

		{if $oForum->getType() != $FORUM_TYPE_CATEGORY}
		<!-- Список тем -->
		<div class="sv-forum_header sv-forum_header-section_page">
			<div class="sv-left_bg">
				<h2>{$aLang.forum_themes_list}</h2>
			</div>
			<div class="sv-right_bg">
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.views}</span>
				<span class="sv-last_msg">{$aLang.last_post}</span>
			</div>
		</div>

		<div class="sv-table_container">
			{if $aTopics}
			<table class="sv-forum_body sv-forum_body-section_page">
				<tbody>
					{foreach from=$aTopics item=oTopic}
						{assign var="oUser" value=$oTopic->getUser()}
						{assign var="oPost" value=$oTopic->getPost()}
						{assign var="oLastUser" value=$oPost->getUser()}
						{assign var="oLastPost" value=$oTopic->getLastPost()}
						<tr>
							<td class="sv-icon_col">
								<a class="bbl{if $oTopic->getDateRead()<=$oPost->getDate()} new{/if} {if $oTopic->getPosition()==1} info{/if}{if $oTopic->getStatus()==1} close{/if}" href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html"></a>
							</td>
							<td class="sv-main_col">
								<h3>
									<a href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html">{$oTopic->getTitle()}</a>
									<span class="sv-go_to_page">{hook run='topic_paging' topic=$oTopic forum=$oForum}</span>
								</h3>
								<span class="sv-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
							</td>
							<td class="sv-answers">{$oTopic->getCountPosts()}</td>
							<td class="sv-views">{$oTopic->getViews()}</td>
							<td class="sv-last_msg">
								{if $oPost}
								<span class="sv-date">{date_format date=$oPost->getDate() format='d.m.Y H:i'}</span>
								<span class="sv-author">{$aLang.by} <a href="{$oLastUser->getUserWebPath()}">{$oLastUser->getLogin()}</a></span>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			{else}
				{$aLang.nothing}
			{/if}
		</div>
		<!--/ Список тем -->
		{/if}

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

		{if $oUserCurrent}
		<ul class="sv-bottom_menu">
			{if $oForum->getType() != $FORUM_TYPE_CATEGORY}
			<!-- тоже пока что так -->
			<li><a class="topbutton" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			{/if}
			<li><a class="topbutton" href="#">{$aLang.mark_all_read}</a></li>
		</ul>
		{/if}

	</div>

</div>
{include file='footer.tpl'}