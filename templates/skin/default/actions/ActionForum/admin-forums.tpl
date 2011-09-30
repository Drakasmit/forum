{assign var="noSidebar" value=true}
{include file='header.tpl'}

<script type='text/javascript' src="{cfg name='path.root.web'}/plugins/forum/templates/skin/default/js/admin.js"></script>

<div id="forum">

	<div class="forum-nav">
		<h2>{$aLang.forum_acp}</h2>
	</div>
	
	<ul class="sv-tom_menu">
		<li><a href="{router page='forum'}admin">{$aLang.forum_acp}</a></li>
		<li class="active"><a href="{router page='forum'}admin/forums">{$aLang.forums}</a></li>
	</ul>

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
							<label for="category_title">Название категории:</label><br />
							<input type="text" id="category_title" name="category_title" value="{$_aRequest.category_title}" class="w100p" /><br />
							<span class="form_note"></span>
						</p>
						<p class="buttons">
							<input type="submit" name="submit_category_add" value="Создать" />
						</p>
					</form>
				</div>
				<div style="width:45%;float:right">
					<h2>Удаление категории</h2>
					<strong>Внимание, при удалении категории так-же удаляются все форумы и топики, связанные с этой категорией. В скором времени будут функции переноса.</strong><br /><br />
					{if $aForums}
					<ul>
					{foreach from=$aForums item=oForum}
						{assign var='aSubForums' value=$oForum->getChildren()}
						<li>
							<a href="#" onclick="AdminCategoryDelete('Продолжить?','{$oForum->getTitle()}','{$oForum->getId()}'); return false">[x]</a>
							<strong>{$oForum->getTitle()}</strong>
							{if $aSubForums}
							<ul style="padding-left: 20px">
								{foreach from=$aSubForums item=oSubForum}
								<li>
									<a href="#" onclick="AdminCategoryForum('Продолжить?','{$oSubForum->getTitle()}','{$oSubForum->getId()}'); return false">[x]</a>
									{$oSubForum->getTitle()}
								</li>
								{/foreach}
							</ul>
							{/if}
						</li>
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
							<label for="forum_title">Название форума:</label><br />
							<input type="text" id="forum_title" name="forum_title" value="{$_aRequest.forum_title}" class="w100p" /><br />
						</p>
						<p>
							<label for="forum_description">Описание:</label><br />
							<input type="text" id="forum_description" name="forum_description" value="{$_aRequest.forum_description}" class="w100p" /><br />
							<span class="form_note"></span>
						</p>
						<p>
							<label for="forum_parent">Выберите родительский форум или категорию:</label><br />
							<select id="forum_parent" name="forum_parent" value="{$_aRequest.forum_parent}">
							{foreach from=$aForums item=oForum}
								{assign var='aSubForums' value=$oForum->getChildren()}
								<option value="{$oForum->getId()}">{$oForum->getTitle()}</option>
								{if $aSubForums}
									{foreach from=$aSubForums item=oSubForum}
									<option value="{$oSubForum->getId()}">--{$oSubForum->getTitle()}</option>
									{/foreach}
								{/if}
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
