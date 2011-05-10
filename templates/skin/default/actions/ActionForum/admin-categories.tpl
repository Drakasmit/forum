{assign var="bNoSidebar" value=true}
{include file='header.tpl'}

<script type='text/javascript' src='{cfg name='path.root.web'}/plugins/forum/templates/skin/default/js/admin.js'></script>

<div id="forum">

	<div class="forum-nav">
		<h2>
			Панель администратора
		</h2>
	</div>
	
	<ul class="sv-tom_menu">
		<li class="active"><a href="{router page='forum'}admin/categories/">Категории форумов</a></li>
		<li><a href="{router page='forum'}admin/forums/">Форумы</a></li>
		<li><a href="{router page='forum'}admin/topics/">Топики</a></li>
	</ul>

	<div class="sv-forum-block">

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>Администрарование категорий</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>
		
		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				<div style="width:45%;float:left;">
					<h2>Добавление категории</h2><br />
					<form action="" method="POST" enctype="multipart/form-data">
					
						<p>
							<label for="category_title">Название категории:</label><br />
							<input type="text" id="category_title" name="category_title" value="{$_aRequest.category_title}" class="w100p" /><br />
							<span class="form_note">Название на уникальность не претендует.</span>
						</p>
						
						<p class="buttons">
							<input type="submit" name="submit_category_add" value="Добавить категорию" />
						</p>
						
					</form>
				</div>
				<div style="width:45%;float:right;">
					<h2>Удаление категории</h2>
					<strong>Внимание, при удалении категории так-же удаляются все форумы и топики, связанные с этой категорией. В скором времени будут функции переноса.</strong><br /><br />
					{foreach from=$aCategories item=oCategory}
						<a href="#" onclick="AdminCategoryDelete('Продолжить?','{$oCategory.obj->getTitle()}','{$oCategory.obj->getId()}'); return false;">[x]</a> {$oCategory.obj->getTitle()}<br />
					{/foreach}
				</div>
			</div>
			<div class="clear"></div>
		</div>

	</div>

</div>
{include file='footer.tpl'}