{assign var="bNoSidebar" value=true}
{include file='header.tpl'}

{if $oConfig->GetValue('view.tinymce')}
<script type="text/javascript" src="{cfg name='path.root.engine_lib'}/external/tinymce_3.2.7/tiny_mce.js"></script>

<script type="text/javascript">
{literal}
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_buttons1 : "lshselect,bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,lslink,unlink,lsvideo,lsimage,pagebreak,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : 0,
	theme_advanced_resizing_use_cookie : 0,
	theme_advanced_path : false,
	object_resizing : true,
	force_br_newlines : true,
    forced_root_block : '', // Needed for 3.x
    force_p_newlines : false,    
    plugins : "lseditor,safari,inlinepopups,media,pagebreak",
    convert_urls : false,
    extended_valid_elements : "embed[src|type|allowscriptaccess|allowfullscreen|width|height]",
    pagebreak_separator :"<cut>",
    media_strict : false,
    language : TINYMCE_LANG
});
{/literal}
</script>

{else}
	{include file='window_load_img.tpl' sToLoad='topic_text'}
{/if}

<div id="forum">

	<div class="topic" style="display: none;">
		<div class="content" id="text_preview"></div>
	</div>

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">Форум</a></span> Новый топик
		</h2>
	</div>

	<div class="sv-forum-block">

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>Создание топика в <a href="{router page='forum'}{$oForum->getUrl()}/">{$oForum->getTitle()}</a></h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>
		
		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<form action="" method="POST" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" /> 
						
						<p><label for="topic_title">{$aLang.topic_create_title}:</label><br />
						<input type="text" id="topic_title" name="topic_title" value="{$_aRequest.topic_title}" class="w100p" /><br />
						<span class="form_note">{$aLang.topic_create_title_notice}</span>
						</p>

						{if !$oConfig->GetValue('view.tinymce')}<div class="note">{$aLang.topic_create_text_notice}</div>{/if}<label for="topic_text">{$aLang.topic_create_text}:</label>
						{if !$oConfig->GetValue('view.tinymce')}
						<div class="sv-top_panel">
							<div class="panel_form">
								<select onchange="lsPanel.putTagAround('topic_text',this.value); this.selectedIndex=0; return false;" style="width: 91px;">
									<option value="">{$aLang.panel_title}</option>
									<option value="h4">{$aLang.panel_title_h4}</option>
									<option value="h5">{$aLang.panel_title_h5}</option>
									<option value="h6">{$aLang.panel_title_h6}</option>
								</select>            			
								<select onchange="lsPanel.putList('topic_text',this); return false;">
									<option value="">{$aLang.panel_list}</option>
									<option value="ul">{$aLang.panel_list_ul}</option>
									<option value="ol">{$aLang.panel_list_ol}</option>
								</select>
								<a href="#" onclick="lsPanel.putTagAround('topic_text','b'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/bold_ru.gif" width="20" height="20" title="{$aLang.panel_b}"></a>
								<a href="#" onclick="lsPanel.putTagAround('topic_text','i'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/italic_ru.gif" width="20" height="20" title="{$aLang.panel_i}"></a>	 			
								<a href="#" onclick="lsPanel.putTagAround('topic_text','u'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/underline_ru.gif" width="20" height="20" title="{$aLang.panel_u}"></a>	 			
								<a href="#" onclick="lsPanel.putTagAround('topic_text','s'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/strikethrough.gif" width="20" height="20" title="{$aLang.panel_s}"></a>	 			
								&nbsp;
								<a href="#" onclick="lsPanel.putTagUrl('topic_text','{$aLang.panel_url_promt}'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/link.gif" width="20" height="20"  title="{$aLang.panel_url}"></a>
								<a href="#" onclick="lsPanel.putQuote('topic_text'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/quote.gif" width="20" height="20" title="{$aLang.panel_quote}"></a>
								<a href="#" onclick="lsPanel.putTagAround('topic_text','code'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/code.gif" width="30" height="20" title="{$aLang.panel_code}"></a>
								<a href="#" onclick="lsPanel.putTagAround('topic_text','video'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/video.gif" width="20" height="20" title="{$aLang.panel_video}"></a>
						
								<a href="#" onclick="showImgUploadForm(); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/img.gif" width="20" height="20" title="{$aLang.panel_image}"></a> 			
								<a href="#" onclick="lsPanel.putText('topic_text','<cut>'); return false;" class="button"><img src="{cfg name='path.static.skin'}/images/panel/cut.gif" width="20" height="20" title="{$aLang.panel_cut}"></a>	
							</div>
						</div>
						{/if}
						<div class="sv-textarea">
							<span class="sv-tl"></span>
							<span class="sv-tr"></span>
							<span class="sv-bl"></span>
							<span class="sv-br"></span>
							<textarea name="topic_text" id="topic_text">{$_aRequest.topic_text}</textarea>
						</div>
						
						{if $oUserCurrent AND $oUserCurrent->isAdministrator()}
						<p>
							<label><input type="checkbox" name="topic_position" id="topic_position" />Закрепить?</label><br />
							<label><input type="checkbox" name="topic_status" id="topic_status" />Закрыть?</label>
						</p>
						{/if}
						
						{hook run='form_add_topic_topic_end'}					
						<p class="buttons">
						<input type="submit" name="submit_topic_publish" value="{$aLang.topic_create_submit_publish}" class="right" />
						<input type="submit" name="submit_preview" value="{$aLang.topic_create_submit_preview}" onclick="$('text_preview').getParent('div').setStyle('display','block'); ajaxTextPreview('topic_text',false); return false;" />&nbsp;
						</p>
					</fieldset>
				</form>
			</div>
			<div class="clear"></div>
		</div>

	</div>

</div>
{include file='footer.tpl'}