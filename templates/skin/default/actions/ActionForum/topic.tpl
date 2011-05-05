{assign var="bNoSidebar" value=true}
{include file='header.tpl'}

<script type="text/javascript" src="http://my.artemeff.ru/plugins/forum/templates/skin/default/js/posts.js"></script>

<div id="forum">

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">Форум</a></span> <span><a href="{router page='forum'}{$oForum->getUrl()}/">{$oForum->getTitle()}</a></span> {$oTopic->getTitle()}
		</h2>
	</div>

	<div class="sv-forum-block">

		<ul class="sv-tom_menu">
			<li class="sv-first"><a class="replyb" href="#">Ответить</a></li>
			<li class="sv-first"><a class="newtopic" href="#">Создать новую тему</a></li>
		</ul>


		<div class="sv-forum_nav">
			<div class="sv-numbers">
				<a class="sv-here" href="#">1</a>
				<a href="#">2</a>
				<a href="#">3</a>
				<a href="#">4</a>
			</div>
		</div>

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
						<img alt="{$oUser->getUserLogin()}" src="{$oUser->getProfileAvatarPath(64)}" class="sv-avavtar" />
						<span class="sv-nickname"><a href="{$oUser->getUserWebPath()}">{$oUser->getUserLogin()}</a></span>
						<span class="sv-msg_count">Статей: <span>13</span></span>
						<span class="sv-msg_count">Комментариев: <span>13</span></span>
						<span class="sv-msg_count">Постов: <span>13</span></span>
						<span class="sv-sbj_count">Тем: <span>13</span></span>
					</div>
					<div class="sv-post_section">
						<div class="sv-post_section1">
							<span class="sv-post_date"><a href="#post-{$oPost->getId()}" name="post-{$oPost->getId()}">#</a> {$oPost->getDate()}</span>
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

		<ul class="sv-bottom_menu">
			<li class="sv-first"><a class="replyb" href="#">Ответить</a></li>
			<li class="sv-first"><a class="newtopic" href="#">Создать новую тему</a></li>
		</ul>

		<div class="sv-forum_nav">
			<div class="sv-numbers">
				<a class="sv-here" href="#">1</a>
				<a href="#">2</a>
				<a href="#">3</a>
				<a href="#">4</a>
			</div>
		</div>


		<div class="clear"></div>

		<div class="sv-forum_header sv-forum_header-fast_answer">
			<div class="sv-left_bg">
				<h2>Быстрый ответ</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<div class="comment"><div class="content"><div class="text" id="comment_preview_0" style="display: none;"></div></div></div>
				<form action="" method="post" id="form_post" onsubmit="return false;" enctype="multipart/form-data">
					<fieldset>
						<div class="sv-top_panel">
							{if !$oConfig->GetValue('view.tinymce')}
            					<div class="panel_form" style="background: #eaecea; margin-top: 2px;">       	 
	 								<a href="#" onclick="lsPanel.putTagAround('form_comment_text','b'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/bold_ru.gif" width="20" height="20" title="{$aLang.panel_b}"></a>
	 								<a href="#" onclick="lsPanel.putTagAround('form_comment_text','i'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/italic_ru.gif" width="20" height="20" title="{$aLang.panel_i}"></a>	 			
	 								<a href="#" onclick="lsPanel.putTagAround('form_comment_text','u'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/underline_ru.gif" width="20" height="20" title="{$aLang.panel_u}"></a>	 			
	 								<a href="#" onclick="lsPanel.putTagAround('form_comment_text','s'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/strikethrough.gif" width="20" height="20" title="{$aLang.panel_s}"></a>	 			
	 								&nbsp;
	 								<a href="#" onclick="lsPanel.putTagUrl('form_comment_text','{$aLang.panel_url_promt}'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/link.gif" width="20" height="20"  title="{$aLang.panel_url}"></a>
	 								<a href="#" onclick="lsPanel.putQuote('form_comment_text'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/quote.gif" width="20" height="20" title="{$aLang.panel_quote}"></a>
	 								<a href="#" onclick="lsPanel.putTagAround('form_comment_text','code'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/code.gif" width="30" height="20" title="{$aLang.panel_code}"></a>
	 							</div>
							{/if}
						</div>
						<div class="sv-textarea">
							<span class="sv-tl"></span>
							<span class="sv-tr"></span>
							<span class="sv-bl"></span>
							<span class="sv-br"></span>
							<textarea name="comment-text" id="form_comment_text"></textarea>
						</div>
						<input type="submit" name="submit_preview" value="{$aLang.comment_preview}" onclick="ForumPost.preview($('form_comment_reply').getProperty('value')); return false;" />&nbsp;
						<input type="submit" name="submit_comment" value="{$aLang.comment_add}" onclick="ForumPost.addComment('form_comment'); return false;">    	
						<input type="hidden" name="reply" value="" id="form_comment_reply" />
						<input type="hidden" name="post_topic_id" value="{$oTopic->getId()}" />
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
		</div>

	</div>
	
</div>

{include file='footer.tpl'}