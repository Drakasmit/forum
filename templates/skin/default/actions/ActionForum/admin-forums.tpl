{assign var="noSidebar" value=true}
{include file='header.tpl'}

<script type='text/javascript' src="{cfg name='path.root.web'}/plugins/forum/templates/skin/default/js/admin.js"></script>

<div class="forum">
	<div class="forum-nav">
		<h2>{$aLang.forum_acp}</h2>
	</div>

	<div class="sv-forum-block">
		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>Администратирование форумов</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>

		<div class="sv-fast_answer clear_fix">
			<div class="sv-fast_answer_form">
				<div style="width:45%;float:left">
					<h2>{$aLang.forum_create_category}</h2><br />
					<form action="" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" /> 
						<p>
							<label for="category_title">{$aLang.forum_create_title}:</label><br />
							<input type="text" id="category_title" name="category_title" value="{$_aRequest.category_title}" class="w100p" /><br />
							<span class="form_note"></span>
						</p>
						<p>
							<label for="category_url">{$aLang.forum_create_url}:</label><br />
							<input type="text" id="category_url" name="category_url" value="{$_aRequest.category_url}" class="w100p" /><br />
							<span class="form_note">{$aLang.forum_create_url_note}</span>
						</p>
						<p class="buttons">
							<input type="submit" name="submit_category_add" value="Создать" />
						</p>
					</form>
				</div>
				<div style="width:45%;float:right">
					<h2>Редактирование форумов</h2>
					<strong>Внимание, при удалении категории так-же удаляются все форумы и топики, связанные с этой категорией. В скором времени будут функции переноса.</strong><br /><br />
					{if $aForums}
					<ul id="forums-tree">
					{foreach from=$aForumsList item=aItem}
						<li id="forum-{$aItem.id}"><a href="#" onclick="return ls.forum.admin.deleteForum({$aItem.id},'{$aItem.title}')" title="{$aLang.forum_delete}">[x]</a> {$aItem.title}</li>
					{/foreach}
					</ul>
					{else}
						{$aLang.forums_no}
					{/if}
				</div>
			</div>
			<div class="clear"></div>
			<div class="sv-fast_answer_form">
				<div style="width:45%;float:left">
					<h2>{$aLang.forum_create}</h2><br />
					{if $aForums}
					<form action="" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" /> 
						<p>
							<label for="forum_title">{$aLang.forum_create_title}:</label><br />
							<input type="text" id="forum_title" name="forum_title" value="{$_aRequest.forum_title}" class="w100p" /><br />
						</p>
						<p>
							<label for="forum_url">{$aLang.forum_create_url}:</label><br />
							<input type="text" id="forum_url" name="forum_url" value="{$_aRequest.forum_url}" class="w100p" /><br />
							<span class="form_note">{$aLang.forum_create_url_note}</span>
						</p>
						<p>
							<label for="forum_description">{$aLang.forum_create_description}:</label><br />
							<input type="text" id="forum_description" name="forum_description" value="{$_aRequest.forum_description}" class="w100p" /><br />
							<span class="form_note"></span>
						</p>
						<p>
							<label for="forum_parent">{$aLang.forum_create_parent}:</label><br />
							<select id="forum_parent" name="forum_parent">
							{foreach from=$aForumsList item=aItem}
								<option value="{$aItem.id}">{$aItem.title}</option>
							{/foreach}
							</select><br />
							<span class="form_note"></span>
						</p>
						<p class="buttons">
							<input type="submit" name="submit_forum_add" value="Создать" />
						</p>
					</form>
					{else}
						{$aLang.forum_create_warning}
					{/if}
				</div>
			</div>
		</div>

	</div>

</div>

{include file='footer.tpl'}