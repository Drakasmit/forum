		{literal}
		<script language="JavaScript" type="text/javascript">
			window.addEvent('domready', function() {
				{/literal}
				ForumPost.setIdPostLast({$iMaxIdPost});
				ForumPost.setIdForum({$oForum->getId()});
				{literal}
			});					
		</script>
		{/literal}

		<div class="comment">
			<div class="content">
				<div class="text" id="post_preview" style="display: none;">

				</div>
			</div>
		</div>

		<div class="sv-forum_header sv-forum_header-fast_answer">
			<div class="sv-left_bg">
				<h2>{$aLang.fast_reply}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<div class="sv-top_panel">
					{if !$oConfig->GetValue('view.tinymce')}
            			<div class="panel_form" style="background: #eaecea; margin-top: 2px;">       	 
	 						<a href="#" onclick="lsPanel.putTagAround('form_post_text','b'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/bold_ru.gif" width="20" height="20" title="{$aLang.panel_b}"></a>
	 						<a href="#" onclick="lsPanel.putTagAround('form_post_text','i'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/italic_ru.gif" width="20" height="20" title="{$aLang.panel_i}"></a>	 			
	 						<a href="#" onclick="lsPanel.putTagAround('form_post_text','u'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/underline_ru.gif" width="20" height="20" title="{$aLang.panel_u}"></a>	 			
	 						<a href="#" onclick="lsPanel.putTagAround('form_post_text','s'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/strikethrough.gif" width="20" height="20" title="{$aLang.panel_s}"></a>	 			
	 						&nbsp;
	 						<a href="#" onclick="lsPanel.putTagUrl('form_post_text','{$aLang.panel_url_promt}'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/link.gif" width="20" height="20"  title="{$aLang.panel_url}"></a>
	 						<a href="#" onclick="lsPanel.putQuote('form_post_text'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/quote.gif" width="20" height="20" title="{$aLang.panel_quote}"></a>
	 						<a href="#" onclick="lsPanel.putTagAround('form_post_text','code'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/code.gif" width="30" height="20" title="{$aLang.panel_code}"></a>
	 					</div>
					{/if}
				</div>
				<form action="" method="post" id="form_post" onsubmit="return false;" enctype="multipart/form-data">
					<fieldset>
						<div class="sv-textarea">
							<span class="sv-bl"></span>
							<span class="sv-br"></span>
							<textarea name="form_post_text" id="form_post_text"></textarea>
						</div>
						<input type="submit" name="submit_preview" value="{$aLang.comment_preview}" onclick="ForumPost.preview($('form_post_reply').getProperty('value')); return false;" />&nbsp;
						<input type="submit" name="submit_post" value="{$aLang.comment_add}"  onclick="ForumPost.addComment('form_post',{$oTopic->getId()}); return false;" />    	
						<input type="hidden" name="reply" value="" id="form_post_reply" />
						<input type="hidden" name="topic_id" value="{$oTopic->getId()}" />
						<input type="hidden" name="forum_id" value="{$oForum->getId()}" />
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
		</div>