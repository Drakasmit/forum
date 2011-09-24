		{literal}
		<script language="JavaScript" type="text/javascript">
			jQuery(document).ready(function(){
				{/literal}
				forum.post.setIdPostLast({$iMaxIdPost});
				forum.post.setIdForum({$oForum->getId()});
				{literal}
			});					
		</script>
		{/literal}

		<div class="comment" id="post_preview" style="display: none;"></div>

		<div class="sv-forum_header sv-forum_header-fast_answer">
			<div class="sv-left_bg">
				<h2>{$aLang.fast_reply}</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<div class="sv-top_panel">
					{include file='window_load_img.tpl' sToLoad='form_post_text'}
					<script type="text/javascript">
					jQuery(document).ready(function($){
						ls.lang.load({lang_load name="panel_b,panel_i,panel_u,panel_s,panel_url,panel_url_promt,panel_code,panel_video,panel_image,panel_cut,panel_quote,panel_list,panel_list_ul,panel_list_ol,panel_title,panel_clear_tags,panel_video_promt,panel_list_li,panel_image_promt"});
						// Подключаем редактор
						$('#form_post_text').markItUp(getMarkitupCommentSettings());
					});
					</script>
				</div>
				<form action="" method="post" id="form_post" onsubmit="return false;" enctype="multipart/form-data">
					<fieldset>
						<div class="sv-textarea">
							<span class="sv-bl"></span>
							<span class="sv-br"></span>
							<textarea name="form_post_text" id="form_post_text"></textarea>
						</div>
						<input type="submit" name="submit_preview" value="{$aLang.comment_preview}" onclick="forum.post.preview();" />&nbsp;
						<input type="submit" name="submit_post" value="{$aLang.comment_add}"  onclick="forum.post.addComment('form_post',{$oTopic->getId()}); return false;" />    	
						<input type="hidden" name="last_post" value="{$iMaxIdPost}" id="form_post_lastpost" />
						<input type="hidden" name="topic_id" value="{$oTopic->getId()}" />
						<input type="hidden" name="forum_id" value="{$oForum->getId()}" />
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
		</div>