{assign var="noSidebar" value=true}
{include file='header.tpl'}

<script type='text/javascript' src='{cfg name='path.root.web'}/plugins/forum/templates/skin/default/js/admin.js'></script>

<div id="forum">

	<div class="forum-nav">
		<h2>
			Панель администратора
		</h2>
	</div>
	
	<ul class="sv-tom_menu">
		<li><a href="{router page='forum'}admin/categories/">Категории форумов</a></li>
		<li class="active"><a href="{router page='forum'}admin/forums/">Форумы</a></li>
		<li><a href="{router page='forum'}admin/topics/">Топики</a></li>
	</ul>

	<div class="sv-forum-block">

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>Администратирование форумов</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>
		
		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				{foreach from=$aCategories item=oCategory}
					<div>
					<a href="#" onclick="AdminCategoryDelete('Продолжить?','{$oCategory.obj->getTitle()}','{$oCategory.obj->getId()}'); return false;">[x]</a> {$oCategory.obj->getTitle()}<br />
					{if $oCategory.forums}
						<ul>
						{foreach from=$oCategory.forums item=oForum}
							<li><a href="#" onclick="AdminForumDelete('Продолжить?','{$oForum->getTitle()}','{$oForum->getId()}'); return false;">[x]</a> {$oForum->getTitle()}<br /></li>
						{/foreach}
						</ul>
					{/if}
					<a href="#" onclick="AdminForumAdd({$oCategory.obj->getId()},this); return false;">Создать</a>
					</div>
				{/foreach}
				<form action="" id="forum_add" style="display:none" method="POST" enctype="multipart/form-data">
					<input type="hidden" id="forum_category_id" name="forum_category_id" value="{$_aRequest.forum_category_id}"/>
					<input type="text" iname="forum_title" value="{$_aRequest.forum_title}" class="w100p" />
					<input type="submit" name="submit_forum_add" value="Добавить" />
				</form>
			</div>
			<div class="clear"></div>
		</div>

	</div>

</div>
{include file='footer.tpl'}
