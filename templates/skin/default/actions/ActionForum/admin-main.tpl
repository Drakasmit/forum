{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div class="forum">

	<div class="forum-nav">
		<h2>{$aLang.forum_acp}</h2>
	</div>

	<div class="sv-forum-block">
		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>{$aLang.forum_plugin_about}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>
		
		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				{$aLang.forum_plugin_about_text}
			</div>
			<div class="clear"></div>
		</div>
	</div>

</div>
{include file='footer.tpl'}