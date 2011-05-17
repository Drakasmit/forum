{assign var="bNoSidebar" value=true}
{include file='header.tpl'}

<script type="text/javascript" src="http://my.artemeff.ru/plugins/forum/templates/skin/default/js/posts.js"></script>

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

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>{$oTopic->getTitle()}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-posts_block">
			{foreach from=$aPost item=oPost}
				{assign var="oUser" value=$oPost->getUser()}
				<div class="sv-post">
					<span class="sv-corners sv-tl"></span>
					<span class="sv-corners sv-tr"></span>
					<div class="sv-personal">
						{if $oTopic->getDateRead()<=$oPost->getDate()}new!{else}old =({/if}
						/{$oTopic->getDateRead()}\
						<img alt="{$oUser->getUserLogin()}" src="{$oUser->getProfileAvatarPath(64)}" class="sv-avavtar" />
						<span class="sv-nickname"><a href="{$oUser->getUserWebPath()}">{$oUser->getUserLogin()}</a></span>
						<span class="sv-msg_count">{$aLang.articles}: <span>13</span></span>
						<span class="sv-msg_count">{$aLang.comments}: <span>13</span></span>
						<span class="sv-msg_count">{$aLang.topics}: <span>13</span></span>
						<span class="sv-sbj_count">{$aLang.posts}: <span>13</span></span>
					</div>
					<div class="sv-post_section">
						<div class="sv-post_section1">
							<span class="sv-post_date"><a {if $oTopic->getDateRead()<=$oPost->getDate()}class="new"{/if} href="#post-{$oPost->getId()}" name="post-{$oPost->getId()}">#</a> {date_format date=$oPost->getDate()}</span>
							<div class="sv-post_body">
								{$oPost->getText()}
							</div>
						</div>
					</div>
					<span class="sv-corners sv-bl"></span>
					<span class="sv-corners sv-br"></span>
					<div class="clear"></div>
				</div>
			{/foreach}
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

		{if $oUserCurrent AND $oTopic->getStatus()!=1 OR $oUserCurrent AND $oUserCurrent->isAdministrator()}
		<div class="sv-forum_header sv-forum_header-fast_answer">
			<div class="sv-left_bg">
				<h2>{$aLang.fast_reply}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<div class="comment"><div class="content"><div class="text" id="comment_preview_0" style="display: none;"></div></div></div>
				<form action="{router page='forum'}addpost/" method="post" id="form_comment" enctype="multipart/form-data">
					<fieldset>
						<div class="sv-top_panel">
							{if !$oConfig->GetValue('view.tinymce')}
            					<div class="panel_form" style="background: #eaecea; margin-top: 2px;">       	 
	 								<a href="#" onclick="lsPanel.putTagAround('post-text','b'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/bold_ru.gif" width="20" height="20" title="{$aLang.panel_b}"></a>
	 								<a href="#" onclick="lsPanel.putTagAround('post-text','i'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/italic_ru.gif" width="20" height="20" title="{$aLang.panel_i}"></a>	 			
	 								<a href="#" onclick="lsPanel.putTagAround('post-text','u'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/underline_ru.gif" width="20" height="20" title="{$aLang.panel_u}"></a>	 			
	 								<a href="#" onclick="lsPanel.putTagAround('post-text','s'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/strikethrough.gif" width="20" height="20" title="{$aLang.panel_s}"></a>	 			
	 								&nbsp;
	 								<a href="#" onclick="lsPanel.putTagUrl('post-text','{$aLang.panel_url_promt}'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/link.gif" width="20" height="20"  title="{$aLang.panel_url}"></a>
	 								<a href="#" onclick="lsPanel.putQuote('post-text'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/quote.gif" width="20" height="20" title="{$aLang.panel_quote}"></a>
	 								<a href="#" onclick="lsPanel.putTagAround('post-text','code'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/code.gif" width="30" height="20" title="{$aLang.panel_code}"></a>
	 							</div>
							{/if}
						</div>
						<div class="sv-textarea">
							<span class="sv-tl"></span>
							<span class="sv-tr"></span>
							<span class="sv-bl"></span>
							<span class="sv-br"></span>
							<textarea name="post_text" id="post_text"></textarea>
						</div>
						<input type="submit" name="submit_preview" value="{$aLang.comment_preview}" onclick="ForumPost.preview($('form_comment_reply').getProperty('value')); return false;" />&nbsp;
						<input type="submit" name="submit_comment" value="{$aLang.comment_add}" />    	
						<input type="hidden" name="reply" value="" id="form_comment_reply" />
						<input type="hidden" name="topic_id" value="{$oTopic->getId()}" />
						<input type="hidden" name="forum_id" value="{$oForum->getId()}" />
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
		</div>
		{/if}

	</div>
	
</div>

{include file='footer.tpl'}